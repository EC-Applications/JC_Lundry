<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Models\Category;
use App\Models\DeliveryMan;
use App\Models\Food;
use App\Models\Order;
use App\Models\Zone;
use Brian2694\Toastr\Facades\Toastr;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\LaundryManagement\Entities\LaundryOrder;
use Modules\LaundryManagement\Entities\LaundryOrderDetails;
use Modules\LaundryManagement\Entities\LaundryZone;

class LaundryOrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index($status, Request $request)
    {
        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }

        if(session()->has('order_filter'))
        {
            $request->merge(json_decode(session('order_filter'), true));
        }

        $zone_id = $request->query('zone_id', 'all');

        LaundryOrder::where(['checked' => 0])->update(['checked' => 1]);

        $orders = LaundryOrder::with(['customer','delivery_type'])
        ->when(isset($request->zone), function($query)use($request){
            return $query->whereIn('zone_id',$request->zone);
        })
        ->when($status == 'pending', function($query){
            return $query->Pending();
        })
        ->when($status == 'confirmed', function ($query) {
            return $query->ConfirmedOrder();
        })
        ->when($status == 'out_for_pickup', function ($query) {
            return $query->OutForPickup();
        })
        ->when($status == 'picked_up', function ($query) {
            return $query->PickedUpByDeliveryman();
        })
        ->when($status == 'arrived', function ($query) {
            return $query->ArrivedAtWarehouse();
        })
        ->when($status == 'processing', function ($query) {
            return $query->Processing();
        })

        ->when($status == 'ready_for_delivery', function ($query) {
            return $query->readyForDelivery();
        })
        ->when($status == 'out_for_delivery', function ($query) {
            return $query->OutForDelivery();
        })
        ->when($status == 'cancelled', function ($query) {
            return $query->Canceled();
        })

        ->when($status == 'delivered', function ($query) {
            return $query->Delivered();
        })

        ->when(isset($request->orderStatus) && $status == 'all', function($query)use($request){
            return $query->whereIn('order_status',$request->orderStatus);
        })
        // ->when(isset($request->order_type), function($query)use($request){
        //     return $query->where('order_type', $request->order_type);
        // })
        ->when(isset($request->from_date)&&isset($request->to_date)&&$request->from_date!=null&&$request->to_date!=null, function($query)use($request){
            return $query->whereBetween('created_at', [$request->from_date." 00:00:00",$request->to_date." 23:59:59"]);
        })
        ->orderBy('created_at', 'desc')
        ->paginate(config('default_pagination'));
        $orderstatus = isset($request->orderStatus)?$request->orderStatus:[];
        $scheduled =0;
        $vendor_ids =[];
        $zone_ids =[];
        $from_date =isset($request->from_date)?$request->from_date:null;
        $to_date =isset($request->to_date)?$request->to_date:null;
        $order_type =isset($request->order_type)?$request->order_type:null;
        $total = $orders->total();

        $deliveryMen = DeliveryMan::laundryDm()->where('zone_id', $zone_id)->doesntHave('suspension_log')->available()->active()->get();
        $deliveryMen = Helpers::deliverymen_list_formatting($deliveryMen);

        $zone = is_numeric($zone_id) ? Zone::findOrFail($zone_id) : null;

        return view('laundrymanagement::admin-views.order.list', compact('deliveryMen','orders', 'status', 'orderstatus', 'scheduled', 'vendor_ids', 'zone_ids', 'from_date', 'to_date', 'total', 'order_type', 'zone'));
    }

    /**
     * Summary of details
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\RedirectResponse
     */
    public function details(Request $request, $id)
    {
        $laundry_order = LaundryOrder::with([
            'details' => function ($query) {
                $query->with(['service', 'laundry_item', 'item_track'])->orderBy('services_id');
        }, 'delivery_type', 'customer'=>function($query){
            return $query->withCount('laundry_orders');
        }])
        ->where('id', $id)
        ->first();

        // return $laundry_order;

        if (isset($laundry_order)) {
            $dm = DeliveryMan::laundryDm()->with('laundry_vehicle_type')->where('zone_id',$laundry_order->zone_id)->active()->get();
            $deliveryMen = Helpers::deliverymen_list_formatting($dm);
            return view('laundrymanagement::admin-views.order.order-view', compact('deliveryMen', 'laundry_order'));
        } else {
            Toastr::info(trans('messages.no_more_orders'));
            return back();
        }
    }

    /**
     * Summary of status
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function status(Request $request)
    {
        $order = LaundryOrder::find($request->id);

        if ((is_null($order['deliveryman_id']) && in_array($request->order_status , ['out_for_delivery','delivered'])) || (is_null($order['pickup_deliveryman_id']) && in_array($request->order_status , ['confirmed','picked_up','out_for_pickup', 'arrived']))) {
            Toastr::warning(trans('messages.please_assign_deliveryman_first'));
            return back();
        }

        if ($request->order_status == 'delivered' && $order['transaction_reference'] == null && $order['payment_method'] != 'cash_on_delivery') {
            Toastr::warning(trans('messages.add_your_paymen_ref_first'));
            return back();
        }
        else if($request->order_status == 'canceled')
        {
            if(in_array($order->order_status, ['delivered','canceled','failed']))
            {
                Toastr::warning(trans('messages.you_can_not_cancel_a_completed_order'));
                return back();
            }
        }

        if ($request->order_status == 'delivered') {
            $order->payment_status = 'paid';
            if($order->delivery_man)
            {
                $dm = $order->delivery_man;
                $dm->increment('order_count');
                $dm->current_orders = $dm->current_orders>1?$dm->current_orders-1:0;
                $dm->save();
            }
        }

        $order->order_status = $request->order_status;
        $order[$request->order_status] = now();
        $order->save();

        if($order->pickup_delivery_man)
        {
            $dm = $order->pickup_delivery_man;
            $dm->increment('order_count');
            $dm->current_orders = $dm->current_orders>1?$dm->current_orders-1:0;
            $dm->save();
        }

        Toastr::success(trans('messages.order').trans('messages.status_updated'));
        return back();
    }

    /**
     * Summary of search
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $orders = LaundryOrder::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('id', 'like', "%{$value}%")
                ->orWhere('order_status', 'like', "%{$value}%")
                ->orWhere('transaction_reference', 'like', "%{$value}%");
            }
        })->limit(50)->get();

        return response()->json([
            'view'=>view('laundrymanagement::admin-views.order.partials._table',compact('orders'))->render()
        ]);
    }

    /**
     * Summary of filter
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function filter(Request $request)
    {
        $request->validate([
            'from_date' => 'required_if:to_date,true',
            'to_date' => 'required_if:from_date,true',
        ]);
        session()->put('order_filter', json_encode($request->all()));
        return back();
    }

    /**
     * Summary of filter_reset
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function filter_reset(Request $request)
    {
        session()->forget('order_filter');
        return back();
    }

    /**
     * Summary of dispatch_list
     * @param mixed $status
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function dispatch_list($status, Request $request)
    {
        if (session()->has('zone_filter') == false) {
            session()->put('zone_filter', 0);
        }

        if(session()->has('order_filter'))
        {
            $request->merge(json_decode(session('order_filter'), true));
        }

        $zone_id = $request->query('zone_id', 'all');


        // Order::where(['checked' => 0])->update(['checked' => 1]);

        $orders = LaundryOrder::with(['customer','delivery_type'])
        ->when(isset($request->zone), function($query)use($request){
            return $query->whereIn('zone_id',$request->zone);
        })
        ->when($status == 'searching_for_deliverymen', function($query){
            return $query->SearchingForDeliveryman();
        })
        ->when($status == 'on_going', function($query){
            return $query->Ongoing();
        })
        ->when(isset($request->from_date)&&isset($request->to_date)&&$request->from_date!=null&&$request->to_date!=null, function($query)use($request){
            return $query->whereBetween('created_at', [$request->from_date." 00:00:00",$request->to_date." 23:59:59"]);
        })
        ->where('zone_id', $zone_id)
        ->orderBy('pickup_schedule_at', 'desc')
        ->orderBy('delivery_schedule_at', 'desc')
        ->paginate(config('default_pagination'));

        $orderstatus = isset($request->orderStatus)?$request->orderStatus:[];
        $from_date =isset($request->from_date)?$request->from_date:null;
        $to_date =isset($request->to_date)?$request->to_date:null;
        $total = $orders->total();

        $deliveryMen = DeliveryMan::laundryDm()->where('zone_id', $zone_id)->active()->get();
        $deliveryMen = Helpers::deliverymen_list_formatting($deliveryMen);

        $zone = is_numeric($zone_id) ? LaundryZone::findOrFail($zone_id) : null;

        return view('laundrymanagement::admin-views.order.distaptch_list', compact('orders', 'status', 'orderstatus',  'from_date', 'to_date', 'total', 'deliveryMen', 'zone'));
    }

    /**
     * Summary of update_shipping
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Order $order
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update_shipping(Request $request,LaundryOrder $order)
    {
        $request->validate([
            'contact_person_name' => 'required',
            'contact_person_number' => 'required',
            'address' => 'required'
        ]);

        $address = [
            'contact_person_name' => $request->contact_person_name,
            'contact_person_number' => $request->contact_person_number,
            'address' => $request->address,
        ];

        $order->destination_address = $address;
        $order->save();
        Toastr::success(trans('messages.destination_address_updated'));
        return back();
    }

    /**
     * Summary of add_payment_ref_code
     * @param \Illuminate\Http\Request $request
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function add_payment_ref_code(Request $request, $id)
    {
        $request->validate([
            'transaction_reference'=>'max:30'
        ]);
        LaundryOrder::where(['id' => $id])->update([
            'transaction_reference' => $request['transaction_reference']
        ]);

        Toastr::success(trans('messages.payment_reference_code_is_added'));
        return back();
    }

}
