<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Restaurant;
use Brian2694\Toastr\Facades\Toastr;
use App\CentralLogics\Helpers;

class BusinessSettingsController extends Controller
{

    public function restaurant_index()
    {
        $restaurant = Helpers::get_restaurant_data();
        return view('vendor-views.business-settings.restaurant-index', compact('restaurant'));
    }

    public function restaurant_setup(Restaurant $restaurant, Request $request)
    {
        $request->validate([
            'gst' => 'required_if:gst_status,1',
        ], [
            'gst.required_if' => trans('messages.gst_can_not_be_empty'),
        ]);

        $off_day = $request->off_day?implode('',$request->off_day):'';
        $restaurant->minimum_order = $request->minimum_order;
        $restaurant->opening_time = $request->opening_time;
        $restaurant->closeing_time = $request->closeing_time;
        $restaurant->off_day = $off_day;
        $restaurant->gst = json_encode(['status'=>$request->gst_status, 'code'=>$request->gst]);
        $restaurant->delivery_charge = $restaurant->self_delivery_system?$request->delivery_charge??0: $restaurant->delivery_charge;
        $restaurant->save();
        Toastr::success(trans('messages.restaurant_settings_updated'));
        return back();
    }

    public function restaurant_status(Restaurant $restaurant, Request $request)
    {
        if($request->menu == "schedule_order" && !Helpers::schedule_order())
        {
            return response()->json(['message'=>trans('messages.schedule_order_disabled_warning')],403);
        }

        if((($request->menu == "delivery" && $restaurant->take_away==0) || ($request->menu == "take_away" && $restaurant->delivery==0)) &&  $request->status == 0 )
        {
            return response()->json(['message'=>trans('messages.can_not_disable_both_take_away_and_delivery')],403);
        }

        if((($request->menu == "veg" && $restaurant->non_veg==0) || ($request->menu == "non_veg" && $restaurant->veg==0)) &&  $request->status == 0 )
        {
            return response()->json(['message'=>trans('messages.veg_non_veg_disable_warning')],403);
        }
        
        $restaurant[$request->menu] = $request->status;
        $restaurant->save();
        return response()->json(['message'=>trans('messages.restaurant_settings_updated')]);
    }

    public function active_status(Request $request)
    {
        $restaurant = Helpers::get_restaurant_data();
        $restaurant->active = $restaurant->active?0:1;
        $restaurant->save();
        return response()->json(['message' => $restaurant->active?trans('messages.restaurant_opened'):trans('messages.restaurant_temporarily_closed')], 200);
    }

    public function update_agreement(Request $request)
    {
        $request->validate([
            'agreement' => 'required|file|mimes:pdf'
        ]);
        $restaurant = Helpers::get_restaurant_data();
        if(!$restaurant->edit_agreement){
            Toastr::success(trans('messages.you_dont_have_permission_to_edit_the_agremeent'));
            return back();
        }
        $restaurant->agreement = Helpers::update('restaurant/agreement/', $restaurant->agreement, 'pdf', $request->file('agreement'));
        $restaurant->save();
        Toastr::success(trans('messages.agremeent_uploaded'));
        return back();
    }
}
