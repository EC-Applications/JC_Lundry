<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Zone;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Support\Facades\DB;


class OrderController extends Controller
{
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $order = Order::with(['restaurant', 'delivery_man.rating'])->withCount(['details','review'])->where(['id' => $request['order_id'], 'user_id' => $request->user()->id])->Notpos()->first();
        if($order)
        {
            $order['restaurant'] = $order['restaurant']?Helpers::restaurant_data_formatting($order['restaurant']):$order['restaurant'];
            $order['delivery_address'] = $order['delivery_address']?json_decode($order['delivery_address']):$order['delivery_address'];
            $order['delivery_man'] = $order['delivery_man']?Helpers::deliverymen_data_formatting([$order['delivery_man']]):$order['delivery_man'];
            unset($order['details']);
        }
        else
        {
            return response()->json([
                'errors' => [
                    ['code' => 'schedule_at', 'message' => trans('messages.not_found')]
                ]
            ], 404);
        }
        return response()->json($order, 200);
    }

    public function place_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'payment_method'=>'required|in:cash_on_delivery,digital_payment',
            'order_type' => 'required|in:take_away,delivery',
            'address' => 'required_if:order_type,delivery',
            'longitude' => 'required_if:order_type,delivery',
            'latitude' => 'required_if:order_type,delivery',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => trans('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }

        $zone_id= $request->header('zoneId');

        if($request->latitude && $request->longitude)
        {
            $point = new Point($request->latitude,$request->longitude);
            $zone = Zone::where('id', $zone_id)->contains('coordinates', $point)->first();
            if(!$zone)
            {
                $errors = [];
                array_push($errors, ['code' => 'coordinates', 'message' => trans('messages.out_of_coverage')]);
                return response()->json([
                    'errors' => $errors
                ], 403);
            }
        }

        $address = [
            'contact_person_name' => $request->contact_person_name?$request->contact_person_name:$request->user()->f_name.' '.$request->user()->f_name,
            'contact_person_number' => $request->contact_person_number?$request->contact_person_number:$request->user()->phone,
            'address_type' => $request->address_type?$request->address_type:'Delivery',
            'address' => $request->address,
            'longitude' => (string)$request->longitude,
            'latitude' => (string)$request->latitude,
        ];
        $response = OrderLogic::place_order($request->user()->id, $request->order_type, $request->data, $request->payment_method, $address);
        if($response[1])
        {
            $customer = $request->user();
            $customer->zone_id = $zone_id;
            $customer->save();
        }

        return response()->json($response[0],$response[1]);
    }

    public function get_order_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $paginator = Order::with(['restaurant', 'delivery_man.rating'])->withCount(['details','review'])->where(['user_id' => $request->user()->id])->whereIn('order_status', ['delivered','canceled','refund_requested','refunded','failed'])->Notpos()->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);
        $orders = array_map(function ($data) {
            $data['delivery_address'] = $data['delivery_address']?json_decode($data['delivery_address']):$data['delivery_address'];
            $data['restaurant'] = $data['restaurant']?Helpers::restaurant_data_formatting($data['restaurant']):$data['restaurant'];
            $data['delivery_man'] = $data['delivery_man']?Helpers::deliverymen_data_formatting([$data['delivery_man']]):$data['delivery_man'];
            return $data;
        }, $paginator->items());
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders
        ];
        return response()->json($data, 200);
    }


    public function get_running_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $paginator = Order::with(['restaurant', 'delivery_man.rating'])->withCount(['details','review'])->where(['user_id' => $request->user()->id])->whereNotIn('order_status', ['delivered','canceled','refund_requested','refunded','failed'])->Notpos()->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $orders = array_map(function ($data) {
            $data['delivery_address'] = $data['delivery_address']?json_decode($data['delivery_address']):$data['delivery_address'];
            $data['restaurant'] = $data['restaurant']?Helpers::restaurant_data_formatting($data['restaurant']):$data['restaurant'];
            $data['delivery_man'] = $data['delivery_man']?Helpers::deliverymen_data_formatting([$data['delivery_man']]):$data['delivery_man'];
            return $data;
        }, $paginator->items());
        $data = [
            'total_size' => $paginator->total(),
            'limit' => $request['limit'],
            'offset' => $request['offset'],
            'orders' => $orders
        ];
        return response()->json($data, 200);
    }

    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $details = OrderDetail::whereHas('order', function($query)use($request){
            return $query->where('user_id', $request->user()->id);
        })->where(['order_id' => $request['order_id']])->get();
        
        if ($details->count() > 0) {
            $details = $details = Helpers::order_details_data_formatting($details);
            return response()->json($details, 200);
        } else {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => trans('messages.not_found')]
                ]
            ], 404);
        }
    }

    public function cancel_order(Request $request)
    {
        $order = Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->Notpos()->first();
        if(!$order){
                return response()->json([
                    'errors' => [
                        ['code' => 'order', 'message' => trans('messages.not_found')]
                    ]
                ], 401);
        }
        else if ($order->order_status == 'pending') {

            $order->order_status = 'canceled';
            $order->canceled = now();
            $order->save();
            Helpers::send_order_notification($order);
            return response()->json(['message' => trans('messages.order_canceled_successfully')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => trans('messages.you_can_not_cancel_after_confirm')]
            ]
        ], 401);
    }

    public function refund_request(Request $request)
    {
        $order = Order::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->Notpos()->first();
        if(!$order){
                return response()->json([
                    'errors' => [
                        ['code' => 'order', 'message' => trans('messages.not_found')]
                    ]
                ], 401);
        }
        else if ($order->order_status == 'delivered') {

            $order->order_status = 'refund_requested';
            $order->refund_requested = now();
            $order->save();
            return response()->json(['message' => trans('messages.refund_request_placed_successfully')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => trans('messages.you_can_not_request_for_refund_after_delivery')]
            ]
        ], 401);
    }

    public function update_payment_method(Request $request)
    {
        $order = Order::where(['user_id' => $request->user()->id, 'group_id' => $request['group_id']])->Notpos()->first();
        if ($order) {
            Order::where(['user_id' => $request->user()->id, 'group_id' => $request['group_id']])->update([
                'payment_method' => 'cash_on_delivery', 'order_status'=>'pending', 'pending'=> now()
            ]);

            $fcm_token = $request->user()->cm_firebase_token;
            $value = Helpers::order_status_update_message('pending');
            try {
                if ($value) {
                    $data = [
                        'title' =>trans('messages.order_placed_successfully'),
                        'description' => $value,
                        'order_id' => $order->id,
                        'image' => '',
                        'type'=>'order_status',
                    ];
                    Helpers::send_push_notif_to_device($fcm_token, $data);
                    DB::table('user_notifications')->insert([
                        'data'=> json_encode($data),
                        'user_id'=>$request->user()->id,
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ]);
                }
                if($order->order_type == 'delivery' && !$order->scheduled)
                {
                    $data = [
                        'title' =>trans('messages.order_placed_successfully'),
                        'description' => trans('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'image' => '',
                    ];
                    Helpers::send_push_notif_to_topic($data, $order->restaurant->zone->deliveryman_wise_topic, 'order_request');
                }

            } catch (\Exception $e) {
                info($e);
            }
            return response()->json(['message' => trans('messages.payment').' '.trans('messages.method').' '.trans('messages.updated_successfully')], 200);
        }
        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => trans('messages.not_found')]
            ]
        ], 401);
    }
}
