<?php

namespace Modules\LaundryManagement\Http\Controllers\Api;

use App\CentralLogics\CouponLogic;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\Coupon;
use Carbon\Carbon;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\LaundryManagement\Entities\LaundryDeliveryType;
use Modules\LaundryManagement\Entities\LaundryItemDetailsTrack;
use Modules\LaundryManagement\Entities\LaundryOrder;
use Modules\LaundryManagement\Entities\LaundryOrderDetails;
use Modules\LaundryManagement\Entities\LaundryZone;
use Modules\LaundryManagement\Entities\Services;
use Modules\LaundryManagement\Transformers\LaundryOrderDetailsResource;
use Modules\LaundryManagement\Transformers\LaundryOrderResource;

class LaundryOrderController extends Controller
{
    /**
     * Summary of order_submit
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function order_submit(Request $request)
    {
        // dd($request->all());

        $validator = Validator::make($request->all(), [
            'laundry_delivery_type_id' => 'required',
            'payment_method' => 'required|in:cash_on_delivery',
            'pickup_coordinates' => 'required',
            'destination_coordinates' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $delivery_type = LaundryDeliveryType::where('id', $request->laundry_delivery_type_id)->first();
        if (!$delivery_type) {
            return response()->json([
                'errors' => [
                    ['code' => 'delivery_type', 'message' => trans('messages.delivery_type_not_found_or_not_active')]
                ]
            ], 401);
        }
        if (!$request->hasHeader('laundryZoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => trans('messages.laundry_zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $zone_id = $request->header('laundryZoneId');
        info($zone_id);
        $delivery_charge = isset($delivery_type)?$delivery_type->charge:0;

        //pick and destination must be one of the zones, check.
        $pickup_points = new Point($request->pickup_coordinates[0],$request->pickup_coordinates[1]);
        $destination_points = new Point($request->destination_coordinates[0],$request->destination_coordinates[1]);
        // $zone = LaundryZone::where(function ($query) use($pickup_points, $destination_points) {
        //     return $query->contains('coordinates', $pickup_points)->where(function ($query) use($destination_points){
        //         return $query->contains('coordinates', $destination_points);
        //     });
        // })->first();
        $pickup_zone = LaundryZone::contains('coordinates', $pickup_points)->first();
        $destination_zone = LaundryZone::contains('coordinates', $destination_points)->first();

        if(!($pickup_zone && $destination_zone))
        {
            $errors = [];
            array_push($errors, ['code' => 'coordinates', 'message' => trans('messages.out_of_coverage')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }

        //Coupon check and apply
        if ($request['coupon_code']) {
            $coupon = Coupon::active()->where(['code' => $request['coupon_code']])->first();
            if (isset($coupon)) {
                $staus = CouponLogic::is_valide($coupon, $request->user()->id , null);
                if($staus==407)
                {
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => trans('messages.coupon_expire')]
                        ]
                    ], 407);
                }
                else if($staus==406)
                {
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => trans('messages.coupon_usage_limit_over')]
                        ]
                    ], 406);
                }
                else if($staus==404)
                {
                    return response()->json([
                        'errors' => [
                            ['code' => 'coupon', 'message' => trans('messages.not_found')]
                        ]
                    ], 404);
                }
                if($coupon->coupon_type == 'free_delivery')
                {
                    $delivery_charge = 0;
                    $coupon = null;
                }
            } else {
                return response()->json([
                    'errors' => [
                        ['code' => 'coupon', 'message' => trans('messages.not_found')]
                    ]
                ], 401);
            }
        }

        $pickup_coordinates = $request->pickup_coordinates;
        $destination_coordinates = $request->destination_coordinates;
        $order_details = [];
        $product_price = 0;
        $order_id = 100000 + LaundryOrder::count() + 1;

        $order = new LaundryOrder;
        $order->id = $order_id;
        $order->user_id = auth('api')->user()->id;
        $order->order_status = 'pending';
        $order->payment_status = 'unpaid';
        $order->payment_method = $request->payment_method;
        $order->pickup_coordinates = new Point($pickup_coordinates[0], $pickup_coordinates[1]);
        $order->destination_coordinates = new Point($destination_coordinates[0], $pickup_coordinates[1]);
        $order->pickup_address = $request->pickup_address;
        $order->destination_address = $request->destination_address;
        $order->bar_code = $order_id;
        $order->laundry_delivery_type_id = $request->laundry_delivery_type_id;
        $order->delivery_charge = $delivery_charge;
        $order->pickup_schedule_at = $request->pickup_schedule;
        $order->delivery_schedule_at = $request->delivery_schedule;
        $order->pending = now();
        $order->distance = $request->distance;
        $order->note = $request->note;
        $order->zone_id = isset($zone_id)?json_decode($zone_id, true)[0]:null;

        foreach ($request->cart as $c) {
            $service = Services::active()->find($c['services_id']);
            if (!$service) {
                return response()->json([
                    'errors' => [
                        ['code' => 'service', 'message' => trans('messages.service_not_available_right_now')]
                    ]
                ], 403);
            }

            foreach ($c['items'] as $item) {
                $or_d = [
                    'laundry_orders_id' => $order_id,
                    'services_id' => $service['id'],
                    'laundry_item_id' => $item['laundry_item_id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'bar_code' => substr($service->name, 0, 3).'_'.$order_id,
                    'created_at' => now(),
                    'updated_at' => now()
                ];

                $order_details[] = $or_d;
                $product_price += $or_d['price']*$or_d['quantity'];
            }

        }
        $total_price = $product_price;
        $tax = BusinessSetting::where('key','laundry_tax')->first()->value??0;
        $total_tax_amount= ($tax > 0)?(($total_price * $tax)/100):0;
        $order->tax_amount= round($total_tax_amount, config('round_up_to_digit'));
        $order->order_amount = round($total_price + $order->delivery_charge + $total_tax_amount , config('round_up_to_digit'));
        try {

            DB::beginTransaction();
            $order->save();

            // Order Details
            LaundryOrderDetails::insert($order_details);
            // Get the details for the inserted order
            $details = LaundryOrderDetails::where('laundry_orders_id', $order->id)->get();
            // Generate barcode tracking details for each item in the order details
            $track_details = [];
            foreach ($details as $detail) {
                for($i=0; $i<$detail['quantity']; $i++) {
                    $track_details[] = [
                        'laundry_order_detail_id' => $detail['id'],
                        'bar_code' => $order->id.'_'.$detail['id'].'_'.$detail['services_id'].'_'.$i,
                    ];
                }
            }
            // Inserted orders items and barcode
            LaundryItemDetailsTrack::insert($track_details);
            DB::commit();

            return response()->json([
                'message' => trans('messages.order_submitted_successfully'),
                'order_id' => $order->id,
                'total_amount' => $order->order_amount
            ], 200);

        } catch (\Exception $ex) {
            DB::rollBack();

            return response()->json([
                'errors' => [
                    [
                        'code' => 'laundry_order',
                        'message' => trans('messages.failed_to_submit_order'),
                        'error' => $ex->getMessage()
                    ]
                ]
            ], 403);
        }
    }

    /**
     * Summary of get_order_list
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_order_list(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => is_null($request->from)? '':'required_with:from|date',
            'from' => is_null($request->to)? '':'required_with:to|date',
            'limit' => 'required|numeric',
            'offset' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $list = LaundryOrder::with(['details', 'delivery_type'])
        ->when($request['from'] && $request['to'], function ($query) use ($request) {
            $query->whereBetween('created_at', [Carbon::parse($request['from'])->startOfDay(), Carbon::parse($request['to'])->endOfDay()]);
        })
        ->where(['user_id' => $request->user()->id])
        ->whereIn('order_status', ['delivered', 'cancelled', 'failed'])
        ->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);

        return response()->json(Helpers::response_formatter(LaundryOrderResource::collection($list), $request['limit'], $request['offset']));
    }

    /**
     * Summary of get_order_details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_order_details(Request $request)
    {
        // dd($request->user()->id);
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $details = LaundryOrder::with([
            'details' => function ($query) {
                $query->with(['laundry_item' => function ($query) {
                            $query->with('services');
                    }, 'service']);
        }, 'delivery_type'])->find($request['order_id']);

        // $details = LaundryOrderDetails::with([
        //     'laundry_item' => function ($query) {
        //         $query->with('services');
        // }, 'laundry_order', 'service'])
        // ->whereHas('laundry_order', function($query)use($request){
        //     return $query->where(['user_id' => $request->user()->id, 'id' => $request['order_id']]);
        // })
        // ->where(['laundry_orders_id' => $request['order_id']])
        // ->get();

        // return response()->json($details);

        // return response()->json(Helpers::response_formatter(LaundryOrderDetailsResource::collection($details)));
        return response()->json(Helpers::response_formatter(LaundryOrderResource::make($details)));
    }

    /**
     * Summary of get_running_orders
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_running_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'to' => is_null($request->from)? '':'required_with:from|date',
            'from' => is_null($request->to)? '':'required_with:to|date',
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }


        $list = LaundryOrder::with(['details', 'delivery_type'])
        ->when($request['from'] && $request['to'], function ($query) use ($request) {
            $query->whereBetween('created_at', [Carbon::parse($request['from'])->startOfDay(), Carbon::parse($request['to'])->endOfDay()]);
        })
        ->where(['user_id' => $request->user()->id])
        ->whereNotIn('order_status', ['delivered', 'cancelled', 'failed'])
        ->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);

        return response()->json(Helpers::response_formatter(LaundryOrderResource::collection($list), $request['limit'], $request['offset']));
    }

    /**
     * Summary of cancel_order
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function cancel_order(Request $request)
    {
        $order = LaundryOrder::where(['user_id' => $request->user()->id, 'id' => $request['order_id']])->first();

        if(!$order){
                return response()->json([
                    'errors' => [
                        ['code' => 'order', 'message' => trans('messages.not_found')]
                    ]
                ], 401);
        }
        else if ($order->order_status == 'pending') {

            $order->order_status = 'cancelled';
            $order->cancelled = now();
            $order->save();

            return response()->json(['message' => trans('messages.order_canceled_successfully')], 200);
        }

        return response()->json([
            'errors' => [
                ['code' => 'order', 'message' => trans('messages.you_can_not_cancel_after_confirm')]
            ]
        ], 401);
    }

    /**
     * Summary of track_order
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function track_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $order = LaundryOrder::with(['details', 'delivery_type'])->where(['id' => $request['order_id'], 'user_id' => $request->user()->id])->first();
        if(!$order)
        {
            return response()->json([
                'errors' => [
                    ['code' => 'schedule_at', 'message' => trans('messages.not_found')]
                ]
            ], 404);
        }

        return response()->json(Helpers::response_formatter(LaundryOrderResource::make($order)), 200);
    }

    /**
     * Summary of get_notifications
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_notifications(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $user = $request->user();
        $orders = LaundryOrder::whereUserId($user->id)->latest()->paginate($request['limit'], ['*'], 'page', $request['offset']);;
        ;
        $notification = [];

        $notificationMessages = [
            'pending' => [
                'key' => 'New Laundry order placed notification',
                'value' => 'Your Laundry order has been placed successfully',
                'status' => 'pending',
            ],
            'confirmed' => [
                'key' => 'Laundry Order confirmed notification',
                'value' => 'Your Laundry order is confirmed',
                'status' => 'confirmed',
            ],
            'out_for_pickup' => [
                'key' => 'Picking up Laundry order notification',
                'value' => 'Deliveryman on the way to pickup, Get ready!',
                'status' => 'out_for_pickup',
            ],
            'picked_up' => [
                'key' => 'Laundry Order picked  up notification',
                'value' => 'Deliveryman picked up your order',
                'status' => 'picked_up',
            ],
            'processing' => [
                'key' => 'Laundry Order processing notification',
                'value' => 'Laundry Order is processing',
                'status' => 'processing',
            ],
            'out_for_delivery' => [
                'key' => 'Laundry Order delivery notification',
                'value' => 'A deliveryman on the way to deliver your Laundry order',
                'status' => 'out_for_delivery',
            ],
            'delivered' => [
                'key' => 'Your Laundry Order has been delivered',
                'value' => 'Your Laundry order is delivered successfully',
                'status' => 'delivered',
            ],
            'cancelled' => [
                'key' => 'Laundry Order cancelled notification',
                'value' => 'Your Laundry order is cancelled',
                'status' => 'cancelled',
            ],
        ];

        if ($orders) {
            foreach ($orders as $order) {
                foreach ($notificationMessages as $notificationData) {
                    if ($order->{$notificationData['status']}) {
                        array_push($notification, [
                            'key' => $notificationData['key'],
                            'value' => $notificationData['value'],
                            'order_id' => $order->id,
                            'status' => $notificationData['status'],
                            'time' => $order->{$notificationData['status']},
                        ]);
                    }
                }
            }
        }

        // Sort the array by time key
        usort($notification, function ($a, $b) {
            $a_timestamp = strtotime($a['time']);
            $b_timestamp = strtotime($b['time']);

            return $b_timestamp - $a_timestamp ;
        });

        return response()->json([
            "limit" => $request['limit'],
            "offset" => $request['offset'],
            "data" => $notification
        ],200);
    }
}
