<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderTransaction;
use App\Models\Zone;
use App\Models\Restaurant;
use App\Models\Shift;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use App\Scopes\RestaurantScope;

class ReportController extends Controller
{
    public function order_index()
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        return view('admin-views.report.order-index');
    }

    public function day_wise_report(Request $request)
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }

        $zone_id = $request->query('zone_id', isset(auth('admin')->user()->zone_id)?auth('admin')->user()->zone_id:'all');
        $zone = is_numeric($zone_id)?Zone::findOrFail($zone_id):null;
        return view('admin-views.report.day-wise-report', compact('zone'));
    }

    public function food_wise_report(Request $request)
    {
        if (session()->has('from_date') == false) {
            session()->put('from_date', date('Y-m-01'));
            session()->put('to_date', date('Y-m-30'));
        }
        $from = session('from_date');
        $to = session('to_date');

        $zone_id = $request->query('zone_id', isset(auth('admin')->user()->zone_id)?auth('admin')->user()->zone_id:'all');
        $restaurant_id = $request->query('restaurant_id', 'all');
        $zone = is_numeric($zone_id)?Zone::findOrFail($zone_id):null;
        $restaurant = is_numeric($restaurant_id)?Restaurant::findOrFail($restaurant_id):null;
        $foods = \App\Models\Food::withoutGlobalScope(RestaurantScope::class)->withCount([
            'orders' => function($query)use($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            },
        ])
        ->when(isset($zone), function($query)use($zone){
            return $query->whereIn('restaurant_id', $zone->restaurants->pluck('id'));
        })
        ->when(isset($restaurant), function($query)use($restaurant){
            return $query->where('restaurant_id', $restaurant->id);
        })
        ->paginate(config('default_pagination'))->withQueryString();

        return view('admin-views.report.food-wise-report', compact('zone', 'restaurant', 'foods'));
    }

    public function order_transaction()
    {
        $order_transactions = OrderTransaction::latest()->paginate(config('default_pagination'));
        return view('admin-views.report.order-transactions', compact('order_transactions'));
    }


    public function set_date(Request $request)
    {
        session()->put('from_date', date('Y-m-d', strtotime($request['from'])));
        session()->put('to_date', date('Y-m-d', strtotime($request['to'])));
        return back();
    }

    public function food_search(Request $request){
        $key = explode(' ', $request['search']);

        $from = session('from_date');
        $to = session('to_date');

        $zone_id = $request->query('zone_id', isset(auth('admin')->user()->zone_id)?auth('admin')->user()->zone_id:'all');
        $restaurant_id = $request->query('restaurant_id', 'all');
        $zone = is_numeric($zone_id)?Zone::findOrFail($zone_id):null;
        $restaurant = is_numeric($restaurant_id)?Restaurant::findOrFail($restaurant_id):null;
        $foods = \App\Models\Food::withoutGlobalScope(RestaurantScope::class)->withCount([
            'orders as order_count' => function($query)use($from, $to) {
                $query->whereBetween('created_at', [$from, $to]);
            },
        ])
        ->when(isset($zone), function($query)use($zone){
            return $query->whereIn('restaurant_id', $zone->restaurants->pluck('id'));
        })
        ->when(isset($restaurant), function($query)use($restaurant){
            return $query->where('restaurant_id', $restaurant->id);
        })
        ->where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })
        ->limit(25)->get();

        return response()->json(['count'=>count($foods),
            'view'=>view('admin-views.report.partials._food_table',compact('foods'))->render()
        ]);
    }


    public function zone_details()
    {
        $zone_reports = Zone::with('deliverymen','orders')->withCount(['orders as assigned_orders'=> function($q){
            $q->where('delivery_man_id','!=', null);
        }, 'orders as unassigned_orders' => function($q){
            $q->where('delivery_man_id','=', null);
        }, 'orders as total_orders' , 'deliverymen as zone_riders', 'orders as delivery_type_order_count' => function($q){
            $q->where('order_type', '=', 'delivery');
        }])->withAvg('orders as avg_delivery_time', 'delivery_time')->paginate(config('default_pagination'));

        // dd($zone_reports->items());
        return view('admin-views.report.zone_details',compact('zone_reports'));
    }
    public function delivered_list($status, Request $request)
    {

        Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = Order::with(['customer', 'restaurant'])
        ->when(isset($request->zone), function($query)use($request){
            return $query->whereHas('restaurant', function($q)use($request){
                return $q->whereIn('zone_id',$request->zone);
            });
        })
        ->when($status == 'scheduled', function($query){
            return $query->whereRaw('created_at <> schedule_at');
        })
        ->when($status == 'delivered', function($query){
            return $query->Delivered();
        })
        ->when(isset($request->vendor), function($query)use($request){
            return $query->whereHas('restaurant', function($query)use($request){
                return $query->whereIn('id',$request->vendor);
            });
        })
        ->when(isset($request->order_type), function($query)use($request){
            return $query->where('order_type', $request->order_type);
        })
        ->when(isset($request->from_date)&&isset($request->to_date)&&$request->from_date!=null&&$request->to_date!=null, function($query)use($request){
            return $query->whereBetween('created_at', [$request->from_date." 00:00:00",$request->to_date." 23:59:59"]);
        })
        ->when($request->query('search'), function ($query) use ($request) {
            $query->where(function ($query) use ($request) {
                $keys = explode(' ', $request['search']);
                foreach ($keys as $key) {
                    $query->orWhere('id', 'LIKE', '%' . $key . '%');
                }
            });
        })
        ->Notpos()
        ->orderBy('schedule_at', 'desc')
        ->paginate(config('default_pagination'));
        $orderstatus = isset($request->orderStatus)?$request->orderStatus:[];
        $scheduled =isset($request->scheduled)?$request->scheduled:0;
        $vendor_ids =isset($request->vendor)?$request->vendor:[];
        $zone_ids =isset($request->zone)?$request->zone:[];
        $from_date =isset($request->from_date)?$request->from_date:null;
        $to_date =isset($request->to_date)?$request->to_date:null;
        $order_type =isset($request->order_type)?$request->order_type:null;
        $total = $orders->total();

        return view('admin-views.report.delivered-wise-report', compact('orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total', 'order_type'));
    }
}
