<?php

namespace Modules\LaundryManagement\Http\Controllers\Api;

use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\DeliveryMan;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Modules\LaundryManagement\Entities\LaundryOrder;
use Modules\LaundryManagement\Entities\LaundryOrderDetails;
use Modules\LaundryManagement\Entities\Services;
use Modules\LaundryManagement\Transformers\DeliverymanResource;
use Modules\LaundryManagement\Transformers\LaundryOrderDetailsResource;
use Modules\LaundryManagement\Transformers\LaundryOrderResource;

class LaundryDMController extends Controller
{
    /**
     * Summary of get_profile
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_profile(Request $request)
    {
        $dm = DeliveryMan::laundryDm()
        ->with([
            'rating',
            'documents',
            'zone'
            ])->withCount([
            'laundry_delivered_orders as delivered_orders_count' => function ($query) {
                return $query->whereNotNull('delivered');
            },
            'laundry_picked_orders as collected_orders_count' => function ($query) {
                return $query->whereNotNull('arrived');
            },
            'laundry_delivered_orders as before_delivered_orders_count' => function ($query) {
                return $query->where('order_status', 'out_for_delivery');
            },
            'laundry_picked_orders as before_picked_orders_count' => function ($query) {
                return $query->whereIn('order_status', ['confirmed', 'out_for_pickup', 'picked_up']);
            }])
            ->where(['auth_token' => $request['token']])->first();

        return response()->json($dm, 200);
    }

    /**
     * Summary of update_profile
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_profile(Request $request)
    {
        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'f_name' => 'required',
            'l_name' => 'required',
            'email' => 'required|unique:delivery_men,email,'.$dm->id,
            'password'=>'nullable|min:6',
            'father_name' => 'required',
        ], [
            'f_name.required' => 'First name is required!',
            'l_name.required' => 'Last name is required!',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        if ($request->has('image')) {
            $imageName = Helpers::update('delivery-man/', $dm->image, 'png', $request->file('image'));
        } else {
            $imageName = $dm->image;
        }

        if ($request['password'] != null && strlen($request['password']) > 5) {
            $pass = bcrypt($request['password']);
        } else {
            $pass = $dm->password;
        }
        $dm->f_name = $request->f_name;
        $dm->l_name = $request->l_name;
        $dm->email = $request->email;
        $dm->image = $imageName;
        $dm->password = $pass;
        $dm->father_name = $request->father_name;
        $dm->updated_at = now();
        $dm->save();

        return response()->json(['message' => 'successfully updated!'], 200);
    }

    /**
     * pick up and delivery list by delivery id
     * @param Request $request
     * @param mixed $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_orders_by_filter(Request $request,$status)
    {
        $request->merge(['status' => $status]);
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:all,out_for_pickup,out_for_delivery,picked_up',
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }
        $orders = LaundryOrder::with('delivery_type')
        ->where(function ($query) use ($dm) {
            return $query->where('deliveryman_id', $dm->id)
            ->orWhere('pickup_deliveryman_id', $dm->id);
        })
        ->when($status == 'all', function ($query) use ($dm) {
                return $query->where('deliveryman_id', $dm->id)->whereIn('order_status', ['delivered', 'cancelled', 'failed']);
        })
        ->when($status != 'all', function ($query) use ($dm, $status) {
            return $query->when($status == 'out_for_pickup', function ($query) use ($dm) {
                return $query->where(['order_status' => 'out_for_pickup', 'pickup_deliveryman_id' => $dm->id])->orderBy('pickup_schedule_at');
            })
            ->when($status == 'out_for_delivery', function ($query) use ($dm) {
                return $query->where(['order_status' => 'out_for_delivery', 'deliveryman_id' => $dm->id])->orderBy('delivery_schedule_at');
            })
            ->when($status == 'picked_up', function ($query) use ($dm) {
                return $query->where(['order_status' => 'picked_up', 'pickup_deliveryman_id' => $dm->id])->orderBy('pickup_schedule_at');
            });
        })
        ->latest()
        ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        // return $orders;
        return response()->json(Helpers::response_formatter(LaundryOrderResource::collection($orders), $request['limit'], $request['offset']));
    }

    /**
     * Summary of get_order_details
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_order_details(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }
       // $order = LaundryOrderDetails::with([
       //     'laundry_item' => function ($query) {
       //         $query->with('services');
       // }, 'laundry_order', 'service', 'laundry_order.delivery_type'])
       // ->whereHas('laundry_order', function($query)use($request, $dm){
          //  return $query->where(function ($query) use ($dm) {
         //       return $query->where('deliveryman_id', $dm['id'])->orWhere('pickup_deliveryman_id', $dm['id']);
       //     })->where('id' , $request['order_id']);
       // })
        //->where(['laundry_orders_id' => $request['order_id']])
        //->get();

        $order = LaundryOrder::with(['details' => function ($query) {
            $query->with(['laundry_item' => function ($query) {
                 return $query->with('services');
             },'service']);
         },'delivery_type'])
         ->where('id', $request['order_id'])
         ->where(function ($query) use($dm){
             return $query->where('deliveryman_id', $dm->id)->orWhere('pickup_deliveryman_id', $dm->id);
         })
         ->first();

        if(!$order)
        {
            return response()->json([
                'errors' => [
                    ['code' => 'order', 'message' => trans('messages.not_found')]
                ]
            ], 404);
        }

        //return response()->json(Helpers::response_formatter(LaundryOrderDetailsResource::collection($order)));
        return response()->json(Helpers::response_formatter(LaundryOrderResource::make($order)));
    }

    /**
     * Summary of update_order_status
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update_order_status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'status' => 'required|in:cancelled,out_for_pickup,picked_up,out_for_delivery,delivered',
            'cancellation_reason' => 'required_if:status,cancelled'
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }
        $order = LaundryOrder::where(function ($query) use ($dm) {
            return $query->where('deliveryman_id', $dm->id)->orWhere('pickup_deliveryman_id', $dm->id);
        })
        ->where('id' , $request['order_id'])
        ->first();

        try {
            if ($order && ($request['status'] == 'picked_up' || $request['status'] == 'cancelled' || $request['status'] == 'out_for_pickup') && $order->pickup_deliveryman_id == $dm->id) {
                $order->order_status = $request['status'];
                $order[$request['status']] = now();
                $order->save();
            }
            if ($order && ($request['status'] == 'delivered' || $request['status'] == 'cancelled' || $request['status'] == 'out_for_delivery') && $order->deliveryman_id == $dm->id) {
                $order->order_status = $request['status'];
                $order[$request['status']] = now();
                $order->payment_status = 'paid';
                $order->save();

                $dm->current_orders = $dm->current_orders>1?$dm->current_orders-1:0;
                $dm->save();

                $dm->increment('order_count');
            }

            if ($order && $request['status'] == 'cancelled') {
                $order->cancellation_reason = $request->cancellation_reason;
                $order->save();
            }

            return response()->json([
                    'message' => 'Status updated',
                    'order_id' => $order->id,
                    'order_status' => $order->order_status
            ], 200);
        } catch (\Exception $ex) {
            return response()->json([
                'message' => 'Status not updated',
                "error" =>  $ex->getMessage()
            ], 200);

        }

    }

     /**
      * Summary of update_order
      * @param \Illuminate\Http\Request $request
      * @return \Illuminate\Http\JsonResponse
      */

    public function update_order(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $product_price = 0;
        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }
        $order = LaundryOrder::where(['id' => $request['order_id'], 'pickup_deliveryman_id' => $dm['id']])->first();

        if (!$order) {
            return response()->json([
                'errors' => [
                    [
                        'code' => 'laundry_order',
                        'message' => trans('messages.not_found'),
                    ]
                ]
            ], 403);
        }

        try {

            DB::beginTransaction();

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

                    //delete if remove item from cart in app
                    if ($item['quantity'] == 0) {
                        LaundryOrderDetails::where('id', $item['details_id'])->delete();
                    }else{
                         //update or insert into order details
                         DB::table('laundry_order_details')->updateOrInsert([
                            'laundry_orders_id' => $request['order_id'],
                            'laundry_item_id' => $item['laundry_item_id'],
                            'services_id' => $service['id']
                        ],
                        [
                            'laundry_orders_id' => $request['order_id'],
                            'services_id' => $service['id'],
                            'laundry_item_id' => $item['laundry_item_id'],
                            'quantity' => $item['quantity'],
                            'price' => $item['price'],
                            'bar_code' => substr($service->name, 0, 3).'_'.$request['order_id'],
                            'updated_at' => now()
                        ]);
                        $product_price += $item['price']*$item['quantity'];
                    }
                }

            }
            $total_price = $product_price;
            $tax = BusinessSetting::where('key','laundry_tax')->first()->value??0;
            $total_tax_amount= ($tax > 0)?(($total_price * $tax)/100):0;
            $order->tax_amount= round($total_tax_amount, config('round_up_to_digit'));
            $order->order_amount = round($total_price + $order->delivery_charge + $total_tax_amount , config('round_up_to_digit'));
            $order->save();

            DB::commit();

            return response()->json([
                'message' => trans('messages.order_updated_successfully'),
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
     * Summary of get_running_orders
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_running_orders(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }
        $orders = LaundryOrder::with('delivery_type')
            ->where(function ($query) use ($dm) {
                return $query->where('pickup_deliveryman_id', $dm->id)
                    ->whereIn('order_status', ['pending', 'confirmed', 'out_for_pickup']);
            })
            ->orWhere(function ($query) use ($dm) {
                return $query->where('deliveryman_id', $dm->id)
                    ->whereIn('order_status', ['ready_for_delivery', 'out_for_delivery']);
        })
        ->latest()
        ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        // return $orders;
        return response()->json(Helpers::response_formatter(LaundryOrderResource::collection($orders), $request['limit'], $request['offset']));
    }


    /**
     * Summary of get_routes
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|array
     */
    public function get_routes(Request $request)
    {
        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }
        $pickup_orders = LaundryOrder::where('pickup_deliveryman_id', $dm['id'])
        ->whereIn('order_status', ['confirmed', 'out_for_pickup'])
        ->get();

        $delivery_orders = LaundryOrder::where('deliveryman_id', $dm['id'])
        ->whereIn('order_status', ['ready_for_delivery', 'out_for_delivery'])
        ->get();

        $coords = array_merge($pickup_orders->pluck('pickup_coordinates')->toArray(), $delivery_orders->pluck('destination_coordinates')->toArray());
        $coord_array = [];
        foreach ($coords as $key => $coord) {
            $coord_array[] = [
                'lat' => $coord->getLat(),
                'lng' => $coord->getLng(),
            ];
        }

        $startPoint = null;
        $lastPoint = null;
        $maxDistance = 0;
        $intermediatePoints = [];

        // Calculate distances and start point, last point findout
        foreach ($coord_array as $i => $point1) {
            foreach (array_slice($coord_array, $i + 1) as $point2) {
                $distance = Helpers::calculateDistance($point1['lat'], $point1['lng'], $point2['lat'], $point2['lng']);

                if(count($coord_array) == 2 && $distance == 0){
                    $startPoint = $point1;
                   $lastPoint = $point2;
               }elseif ($distance > $maxDistance) {
                   $maxDistance = $distance;
                   $startPoint = $point1;
                   $lastPoint = $point2;
               }
            }
        }
        // Assign the remaining coordinates as intermediate points
        foreach ($coord_array as $coordinate) {
            if ($coordinate != $startPoint && $coordinate != $lastPoint) {
                $intermediatePoints[] = $coordinate;
            }
        }

        $result['routes'] = Helpers::get_routes($startPoint, $lastPoint, $intermediatePoints = [], ['DRIVE']); //["DRIVE", "TWO_WHEELER"]
        $result['orders'] = $pickup_orders->merge($delivery_orders);


        return $result;

    }

    /**
     * Summary of save_picture pickup and delivery picture
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function save_picture(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'picture' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }
        $order = LaundryOrder::where('id',$request->order_id)
        ->where(function ($query) use ($dm) {
            return $query->where('deliveryman_id', $dm->id)
            ->orWhere('pickup_deliveryman_id', $dm->id);
        })
        ->first();

        if ($order && in_array($order->order_status, ['out_for_pickup', 'picked_up'])) {
            $column = 'pickup_picture';
            $directory = 'order-pickup/';
        } elseif ($order && in_array($order->order_status, ['out_for_delivery', 'delivered'])) {
            $column = 'delivery_picture';
            $directory = 'order-delivery/';
        } else {
            return response()->json([
                'message' => trans('messages.picture_saving_failed')
            ],200);
        }

        if (is_null($order[$column])) {
            $order->$column = Helpers::upload($directory, 'png', $request->file('picture'));
        } else {
            $order->$column = $request->has('picture')?Helpers::update($directory, $order->$column, 'png', $request->file('picture')):$request->$column;
        }
        //save pictures
        $order->save();

        return response()->json([
            'message' => trans('messages.picture_saved_succesfully')
        ],200);
    }



    /**
     * Summary of get_notifications
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|array
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

        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }
        $orders = LaundryOrder::where(function ($query) use ($dm) {
            return $query->where('deliveryman_id', $dm->id)
            ->orWhere('pickup_deliveryman_id', $dm->id);
        })
        ->latest()
        ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        $notification = [];

        $notificationMessages = [
            'ready_for_delivery' => [
                'key' => 'New order assigning notification',
                'value' => 'Your have assigned to deliver a new order',
                'status' => 'ready_for_delivery',
            ],
            'confirmed' => [
                'key' => 'New order assigning notification',
                'value' => 'Your have assigned to pickup a new order',
                'status' => 'confirmed',
            ],
        ];

        if ($orders) {
            foreach ($orders as $order) {
                foreach ($notificationMessages as $notificationData) {
                    if ($order->{$notificationData['status']}) {
                        if (
                                $dm['id'] == $order->deliveryman_id &&
                                $order->ready_for_delivery &&
                                $notificationData['status'] === 'ready_for_delivery'
                            ) {
                            array_push($notification, [
                              'key' => $notificationData['key'],
                              'value' => $notificationData['value'],
                              'order_id' => $order->id,
                              'status' => $notificationData['status'],
                              'time' => $order->{$notificationData['status']},
                            ]);
                          } else if (
                                $dm['id'] == $order->pickup_deliveryman_id &&
                                $order->confirmed && $notificationData['status'] === 'confirmed'
                            ) {
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


        /**
     * pick up and delivery list by delivery id
     * @param Request $request
     * @param mixed $status
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_orders_history(Request $request,$status)
    {
        $request->merge(['status' => $status]);
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:all,picked_up,delivered',
            'limit' => 'required|numeric',
            'offset' => 'required|numeric',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $dm = DeliveryMan::laundryDm()->where(['auth_token' => $request['token']])->first();
        if (!$dm) {
            return response()->json([
                'errors' => [
                    ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                ]
            ], 401);
        }
        $orders = LaundryOrder::with('delivery_type')
        ->where(function ($query) use ($dm) {
            return $query->where('deliveryman_id', $dm->id)
            ->orWhere('pickup_deliveryman_id', $dm->id);
        })
        ->when($status == 'all', function ($query) use ($dm) {
                return $query->where('deliveryman_id', $dm->id)
                    ->whereIn('order_status', ['delivered', 'cancelled', 'failed']);
        })
        ->when($status != 'all', function ($query) use ($dm, $status) {
            return $query->when($status == 'picked_up', function ($query) use ($dm) {
                return $query->where('pickup_deliveryman_id' , $dm->id)
                    ->whereNotNull('picked_up')->orderBy('pickup_schedule_at');
            })
            ->when($status == 'delivered', function ($query) use ($dm) {
                return $query->where('pickup_deliveryman_id' , $dm->id)
                    ->whereNotNull('delivered')->orderBy('delivery_schedule_at');
            });
        })
        ->latest()
        ->paginate($request['limit'], ['*'], 'page', $request['offset']);

        // return $orders;
        return response()->json(
            Helpers::response_formatter(
                LaundryOrderResource::collection($orders), $request['limit'], $request['offset']
            )
        );
    }
}
