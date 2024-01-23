<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\LaundryManagement\Entities\LaundryItemDetailsTrack;
use Modules\LaundryManagement\Entities\LaundryOrder;
use Modules\LaundryManagement\Entities\LaundryOrderDetails;

class FacilityRoomTrackController extends Controller
{
    /**
     * Summary of facility_room_check_index
     * @param Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function facility_room_check_index()
    {
        return view('laundrymanagement::admin-views.facility-room.facility_room_check_index');
    }

    /**
     * Summary of facility_room_check
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function facility_room_check(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bar_code' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $code = $request->bar_code;
        $order = LaundryOrder::with([
            'details' => function ($query) {
                $query->with(['service', 'laundry_item', 'item_track'])->orderBy('services_id');
        }, 'delivery_type', 'customer'=>function($query){
            return $query->withCount('laundry_orders');
        }])
        ->where('bar_code', $code)
        ->first();

        if ($order) {

            if (!$order->picked_up) {
                return response()->json([
                    'success' => 0,
                    'message' => 'Order not picked up yet'
                ]);
            }
            if ($order->arrived) {
                return response()->json([
                    'success' => 1,
                    'message'=> 'Order already in facility room'
                ]);
            }

            $order->arrived = now();
            $order->order_status = 'arrived';

            if($order->pickup_delivery_man)
            {
                $dm = $order->pickup_delivery_man;
                $dm->increment('order_count');
                $dm->current_orders = $dm->current_orders>1?$dm->current_orders-1:0;
                $dm->save();
            }
            
//            $order->processing = now();
            $order->save();
            return response()->json([
                'success' => 1,
                'message'=> 'The order is marked as arrivied in-house',
                'view'=>view('laundrymanagement::admin-views.facility-room.partials._facility_order_table', compact('order'))->render()
            ]);
        }

        return response()->json([
            'success' => 0,
            'message' => 'Order not update'
        ]);

        // Toastr::success(trans('messages.order_updated_successfully'));
        // return back();
    }

    /**
     * Summary of processing_items
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function processing_items(Request $request)
    {
        $code = $request->bar_code ?? null;

        $order = LaundryOrder::with([
            'details' => function ($query) {
                return $query->with(['laundry_item', 'item_track']);
        }])->where('bar_code', $code)->first();

        return view('laundrymanagement::admin-views.facility-room.processing_items', compact('order'));

    }

    /**
     * Summary of get_items_barcode
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_items_barcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bar_code' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $order = LaundryOrder::with([
            'details' => function ($query) {
                return $query->with(['laundry_item', 'item_track']);
        }])
        ->where('bar_code', $request->bar_code)
        ->first();

        if ($order) {
            $processed = false;
            if (!$order->processing){
                $order->processing = now();
                $order->order_status = 'processing';
                $order->save();
                $processed = true;
            }
            $sectionName = 'processed';
            return response()->json([
                'success' => 1,
                'message'=> $processed ? 'The order is being processing, collect item wise barcode' : 'Order : '.$order->id.' already in processing stage, can collect item wise barcode',
                'view'=> view('laundrymanagement::admin-views.facility-room.partials._barcode_table', compact('order', 'sectionName'))->render()
            ]);
        }
        return response()->json([
            'success' => 0,
            'message' => 'Order not found'
        ]);

    }

    /**
     * Summary of ready_for_delivery_index
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function ready_for_delivery_index(Request $request)
    {
        return view('laundrymanagement::admin-views.facility-room.ready_for_delivery');
    }

    /**
     * Summary of ready_for_delivery
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function ready_for_delivery(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bar_code' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $product = LaundryItemDetailsTrack::with('details.laundry_item')->where('bar_code', $request->bar_code)->first();
        if ($product) {
            $ready = false;
            if (!$product->processed)
            {
                $product->processed = now();
                $product->save();
            }

            $order_id = explode('_',$product->bar_code);
            $order_details = LaundryOrderDetails::withCount([
                'item_track' => function ($query) {
                    return $query->whereNotNull('processed');
            }])
            ->where('laundry_orders_id', $order_id)
            ->get();

            $order = LaundryOrder::with([
                'details' => function ($query){
                    return $query->with(['laundry_item','item_track']);
            }])->where('id',$order_id)->first();

            if ($order_details->sum('quantity') == $order_details->sum('item_track_count')) {
                $order['ready_for_delivery'] = now();
                $order['order_status'] = 'ready_for_delivery';
                $order->save();
                $ready = true;
            }

            $sectionName = 'for_delivery';
            return response()->json([
                'success' => 1,
                'message'=> $ready ? 'The order - '.$order->id.' is fully ready to deliver' : 'Item of order - '.$order->id.' is marked as ready to deliver',
                'view'=> view('laundrymanagement::admin-views.facility-room.partials._barcode_table', compact('order','product', 'sectionName'))->render()
            ]);
        }

        return response()->json([
            'success' => 0
        ]);

    }
}
