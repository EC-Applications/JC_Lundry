<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Log;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {



        $restaurant_id = $request->query('restaurant_id',null);
        $restaurant= $restaurant_id?Restaurant::find($restaurant_id):null;
        $search =$request->query('search',null);
        $key = explode(' ', $request['search']);
        $logs = Log::with(['food', 'food.restaurant'])
        ->when($restaurant_id, function($query)use($restaurant_id){
            $query->whereHas('food',function($q)use($restaurant_id){
                return $q->where('restaurant_id', $restaurant_id);
            });
        })
        ->when($search, function($query)use($search){
            $key=explode(' ', $search);
            $query->whereHas('food',function($q)use($key){
                // dd($key);
                $q->whereHas('restaurant',function($q) use($key){
                    $q->where(function($q) use($key){
                        foreach ($key as $value) {
                            $q->orWhere('name', 'like', "%{$value}%");
                        }
                    });
                })->orWhere(function($q) use($key){
                    foreach ($key as $value) {
                        $q->orWhere('name', 'like', "%{$value}%");
                    }
                });
            });
        })
        ->when(isset($request->start_date)&&isset($request->end_date), function($query)use($request){
            return $query->whereBetween('created_at', [$request->start_date." 00:00:00",$request->end_date." 23:59:59"]);
        })
        ->latest()->paginate(config('default_pagination'));
        return view('admin-views.log.activity_log', compact('logs','restaurant'));
    }
}
