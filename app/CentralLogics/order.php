<?php

namespace App\CentralLogics;

use Exception;
use App\Models\Deal;
use App\Models\Food;
use App\Models\Admin;
use App\Models\Order;
use App\Models\Coupon;
use App\Models\Restaurant;
use App\Models\AdminWallet;
use App\Models\OrderDetail;
use Illuminate\Support\Str;
use App\Models\ItemCampaign;
use App\CentralLogics\Helpers;
use App\Models\BusinessSetting;
use App\Models\OrderTransaction;
use App\Models\RestaurantWallet;
use App\Models\DeliveryManWallet;
use App\CentralLogics\CouponLogic;
use App\Models\OrderLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class OrderLogic
{
    public static function gen_unique_id()
    {
        return rand(1000, 9999) . '-' . Str::random(5) . '-' . time();
    }
    
    public static function track_order($order_id)
    {
        return Helpers::order_data_formatting(Order::with(['details', 'delivery_man.rating'])->where(['id' => $order_id])->first(), false);
    }

    public static function place_order($customer_id, $order_type, $data, $payment_method, $address)
    {
        $last_order = Order::orderBy('group_id', 'desc')->first();
        $order_group_id = $last_order ? $last_order->group_id + 1 : 100001;
        $orders=[];
        foreach($data as $item)
        {
            $coupon = null;
            $delivery_charge = null;
            $schedule_at = $item['schedule_at']?\Carbon\Carbon::parse($item['schedule_at']):now();
            if($item['schedule_at'] && $schedule_at < now())
            {
                return [[
                    'errors' => [
                        ['code' => 'order_time', 'message' => trans('messages.you_can_not_schedule_a_order_in_past')]
                    ]
                ], 406];
            }
            $restaurant = Restaurant::with('discount')->where('id', $item['restaurant_id'])->first();

            if(!$restaurant)
            {
                return [[
                    'errors' => [
                        ['code' => 'order_time', 'message' => trans('messages.restaurant_not_found')]
                    ]
                ], 404];
            }

            if($item['schedule_at'] && !$restaurant->schedule_order)
            {
                return [[
                    'errors' => [
                        ['code' => 'schedule_at', 'message' => trans('messages.schedule_order_not_available')]
                    ]
                ], 406];
            }
            
            if($restaurant->opening_time > $restaurant->closeing_time)
            {
                $restaurant->closeing_time->addHours(12);
            }

            if($restaurant->opening_time->format('H:i') > $schedule_at->format('H:i') && $restaurant->closeing_time->format('H:i') < $schedule_at->format('H:i'))
            {
                return [[
                    'errors' => [
                        ['code' => 'order_time', 'message' => trans('messages.restaurant_is_closed_at_order_time')]
                    ]
                ], 406];
            }

            if(str_contains($restaurant->off_day, $schedule_at->dayOfWeek))
            {
                return [[
                    'errors' => [
                        ['code' => 'order_time', 'message' => trans('messages.scheduled_date_is_restaurant_offday')]
                    ]
                ], 406];
            }

            if ($item['coupon_code']) {
                $coupon = Coupon::active()->where(['code' => $item['coupon_code']])->first();
                if (isset($coupon)) {
                    $staus = CouponLogic::is_valide($coupon, $customer_id ,$item['restaurant_id']);
                    if($staus==407)
                    {
                        return [[
                            'errors' => [
                                ['code' => 'coupon', 'message' => trans('messages.coupon_expire')]
                            ]
                        ], 407];
                    }
                    else if($staus==406)
                    {
                        return [[
                            'errors' => [
                                ['code' => 'coupon', 'message' => trans('messages.coupon_usage_limit_over')]
                            ]
                        ], 406];
                    }
                    else if($staus==404)
                    {
                        return [[
                            'errors' => [
                                ['code' => 'coupon', 'message' => trans('messages.not_found')]                            
                            ]
                        ], 404];
                    }
                    if($coupon->coupon_type == 'free_delivery')
                    {
                        $delivery_charge = 0;
                        $coupon = null;
                    }
                } else {
                    return [[
                        'errors' => [
                            ['code' => 'coupon', 'message' => trans('messages.not_found')]
                        ]
                    ], 401];
                }
            }

            //delivery charge
            $per_km_shipping_charge = (float)BusinessSetting::where(['key' => 'per_km_shipping_charge'])->first()->value;
            $minimum_shipping_charge = (float)BusinessSetting::where(['key' => 'minimum_shipping_charge'])->first()->value;

            $original_delivery_charge = ($item['distance'] * $per_km_shipping_charge > $minimum_shipping_charge) ? $item['distance'] * $per_km_shipping_charge : $minimum_shipping_charge;
            if($order_type != 'take_away' && !$restaurant->free_delivery && $delivery_charge == null)
            {
                if($restaurant->self_delivery_system)
                {
                    $delivery_charge = $restaurant->delivery_charge;
                    $original_delivery_charge = $restaurant->delivery_charge;
                }elseif($restaurant->restaurant_wise_sc_status){
                    $delivery_charge = ($item['distance'] * $restaurant->per_km_shipping_charge > $restaurant->minimum_shipping_charge) ? $item['distance'] * $restaurant->per_km_shipping_charge : $restaurant->minimum_shipping_charge;
                    $original_delivery_charge = $delivery_charge;
                }else
                {
                    $delivery_charge = ($item['distance'] * $per_km_shipping_charge > $minimum_shipping_charge) ? $item['distance'] * $per_km_shipping_charge : $minimum_shipping_charge;
                }
            }

            
            $total_addon_price = 0;
            $product_price = 0;
            $restaurant_discount_amount = 0;

            $order_details = [];
            $order = new Order();
            $order->id = 100000 + Order::all()->count() + 1 + count($orders);
            if (Order::find($order->id)) {
                $order->id = Order::orderBy('id','desc')->first()->id + 1 + count($orders);
            }
            $order->group_id = $order_group_id;
            $order->user_id = $customer_id;
            $order->payment_status = 'unpaid';
            $order->order_status = $payment_method=='digital_payment'?'failed':'pending';
            $order->coupon_code = $item['coupon_code'];
            $order->payment_method = $payment_method;
            $order->transaction_reference = null;
            $order->order_note = $item['order_note'];
            $order->order_type = $order_type;
            $order->restaurant_id = $item['restaurant_id'];
            $order->delivery_charge = round(ceil($delivery_charge*2)/2, config('round_up_to_digit'))??0;
            $order->original_delivery_charge = round(ceil($original_delivery_charge*2)/2, config('round_up_to_digit'));
            $order->delivery_address = json_encode($address);
            $order->schedule_at = $schedule_at;
            $order->scheduled = $item['schedule_at']?1:0;
            $order->otp = rand(1000, 9999);
            $order->zone_id = $restaurant->zone_id;
            $order->pending = now();
            $order->created_at = now();
            $order->updated_at = now();
            foreach ($item['cart'] as $c) {
                if (isset($c['item_campaign_id'])) {
                    $product = ItemCampaign::active()->find($c['item_campaign_id']);
                    if ($product) {
                        if (count(json_decode($product['variations'], true)) > 0) {
                            $price = Helpers::variation_price($product, json_encode($c['variation']));
                        } else {
                            $price = $product['price'];
                        }
                        $product->tax = $restaurant->tax;
                        $product = Helpers::product_data_formatting($product);
                        $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::whereIn('id',$c['add_on_ids'])->get(), $c['add_on_qtys']);
                        $or_d = [
                            'food_id' => null,
                            'item_campaign_id' => $c['item_campaign_id'],
                            'food_details' => json_encode($product),
                            'quantity' => $c['quantity'],
                            'price' => $price,
                            'tax_amount' => Helpers::tax_calculate($product, $price),
                            'discount_on_food' => Helpers::product_discount_calculate($product, $price, $restaurant),
                            'discount_type' => 'discount_on_product',
                            'variant' => json_encode($c['variant']),
                            'variation' => json_encode($c['variation']),
                            'add_ons' => json_encode($addon_data['addons']),
                            'total_add_on_price' => $addon_data['total_add_on_price'],
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $order_details[] = $or_d;
                        $total_addon_price += $or_d['total_add_on_price'];
                        $product_price += $price*$or_d['quantity'];
                        $restaurant_discount_amount += $or_d['discount_on_food']*$or_d['quantity'];
                    } else {
                        return [['errors' => [['code' => 'campaign', 'message' => trans('messages.product_unavailable_warning')]]], 401];
                    }
                } elseif(isset($c['food_id'])) {
                    $product = Food::active()->find($c['food_id']);
                    if ($product) {
                        if (count(json_decode($product['variations'], true)) > 0) {
                            $price = Helpers::variation_price($product, json_encode($c['variation']));
                        } else {
                            $price = $product['price'];
                        }
                        $product->tax = $restaurant->tax;
                        $product = Helpers::product_data_formatting($product);
                        $addon_data = Helpers::calculate_addon_price(\App\Models\AddOn::whereIn('id',$c['add_on_ids'])->get(), $c['add_on_qtys']);
                        $or_d = [
                            'food_id' => $c['food_id'],
                            'item_campaign_id' => null,
                            'food_details' => json_encode($product),
                            'quantity' => $c['quantity'],
                            'price' => round($price, config('round_up_to_digit')),
                            'tax_amount' => round(Helpers::tax_calculate($product, $price), config('round_up_to_digit')),
                            'discount_on_food' => Helpers::product_discount_calculate($product, $price, $restaurant),
                            'discount_type' => 'discount_on_product',
                            'variant' => json_encode($c['variant']),
                            'variation' => json_encode($c['variation']),
                            'add_ons' => json_encode($addon_data['addons']),
                            'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                            'created_at' => now(),
                            'updated_at' => now()
                        ];
                        $total_addon_price += $or_d['total_add_on_price'];
                        $product_price += $price*$or_d['quantity'];
                        $restaurant_discount_amount += $or_d['discount_on_food']*$or_d['quantity'];
                        $order_details[] = $or_d;
                    } else {
                        return [['errors' => [['code' => 'food', 'message' => trans('messages.product_unavailable_warning')]]], 401];
                    }
                } else {
                    $product = Deal::active()->available($schedule_at->format('Y-m-d H:i:s'))->find($c['id']);
                    if(!$product) {
                        return [['errors' => [['code' => 'deal', 'message' => trans('messages.deal_unavailable_warning')]]], 401];
                    }

                    $addon_data = Helpers::calculate_deal_addon_price($c['options'], $c['quantity']);

                    $or_d = [
                        'deal_id'=>$product->id,
                        'food_id' => null,
                        'item_campaign_id' => null,
                        'food_details' => json_encode($c),
                        'quantity' => $c['quantity'],
                        'price' => round($product->price, config('round_up_to_digit')),
                        'tax_amount' => 0,
                        'discount_on_food' => 0,
                        'discount_type' => 'discount_on_product',
                        'variant' => json_encode([]),
                        'variation' => json_encode([]),
                        'add_ons' => json_encode($addon_data['addons']),
                        'total_add_on_price' => round($addon_data['total_add_on_price'], config('round_up_to_digit')),
                        'created_at' => now(),
                        'updated_at' => now()
                    ];
                    $total_addon_price += $or_d['total_add_on_price'];
                    $product_price += $or_d['price']*$or_d['quantity'];
                    $restaurant_discount_amount += $or_d['discount_on_food']*$or_d['quantity'];
                    $order_details[] = $or_d;

                }

            }
            $order->details = $order_details; 
            $restaurant_discount = Helpers::get_restaurant_discount($restaurant);
            if(isset($restaurant_discount))
            {
                if($product_price + $total_addon_price < $restaurant_discount['min_purchase'])
                {
                    $restaurant_discount_amount = 0;
                }

                if($restaurant_discount['max_discount'] > 0 && $restaurant_discount_amount > $restaurant_discount['max_discount'])
                {
                    $restaurant_discount_amount = $restaurant_discount['max_discount'];
                }
            }
            $coupon_discount_amount = $coupon ? CouponLogic::get_discount($coupon, $product_price + $total_addon_price - $restaurant_discount_amount) : 0; 
            $total_price = $product_price + $total_addon_price - $restaurant_discount_amount - $coupon_discount_amount;

            $tax = $restaurant->tax;
            $total_tax_amount= ($tax > 0)?(($total_price * $tax)/100):0;

            if($restaurant->minimum_order > $product_price + $total_addon_price )
            {
                return [['errors' => [['code' => 'order_time', 'message' => trans('messages.you_need_to_order_at_least', ['amount'=>$restaurant->minimum_order.' '.Helpers::currency_code()])]]], 406];
            }

            $free_delivery_over = BusinessSetting::where('key', 'free_delivery_over')->first()->value;
            if(isset($free_delivery_over))
            {
                if($free_delivery_over <= $product_price + $total_addon_price - $coupon_discount_amount - $restaurant_discount_amount)
                {
                    $order->delivery_charge = 0;
                }
            }

            if($coupon)
            {
                $coupon->increment('total_uses');
            }

            $order->coupon_discount_amount = round($coupon_discount_amount, config('round_up_to_digit'));
            $order->coupon_discount_title = $coupon ? $coupon->title : ''; 

            $order->restaurant_discount_amount= round($restaurant_discount_amount, config('round_up_to_digit'));
            $order->total_tax_amount= round($total_tax_amount, config('round_up_to_digit'));
            $order->order_amount = round($total_price + $total_tax_amount + $order->delivery_charge , config('round_up_to_digit'));

            $orders[] = $order;
            if(BusinessSetting::where('key','multi_restaurant_order')->first()->value == '0')
            {
                break;
            }
        }
        
        try{
            DB::beginTransaction();
            foreach($orders as $order)
            {
                $details = $order->details;
                unset($order['details']);
                $order->save();
                foreach ($details as $key => $value) {
                    $details[$key]['order_id'] = $order->id;
                }
                OrderDetail::insert($details);
                Helpers::send_order_notification($order);
                $order->restaurant->increment('total_order');
            }
            DB::commit();
            return [[
                'message' => trans('messages.order_placed_successfully'),
                'group_id' => $order_group_id,
            ], 200];
        }catch(\Exception $ex)
        {
            info($ex);
            DB::rollBack();
            return [[
                'errors' => [
                    ['code' => 'order_time', 'message' => trans('messages.failed_to_place_order')]
                ]
            ], 403];
        }
        
    }

    public static function updated_order_calculation($order)
    {
        return true;
    }
    public static function create_transaction($order, $received_by=false, $status = null)
    {
        $comission = $order->restaurant->comission==null?\App\Models\BusinessSetting::where('key','admin_commission')->first()->value:$order->restaurant->comission;
        $order_amount = $order->order_amount - $order->delivery_charge - $order->total_tax_amount;
        $comission_amount = $comission?($order_amount/ 100) * $comission:0;

        // Delivery Time Calculation
        if($order->delivery_man_id && $order->order_type == 'delivery'){
            $processing_time = Carbon::parse($order->processing);
            $delivery_time = Carbon::parse($order->delivered);
            $time_difference_in_min = $delivery_time->diffInMinutes($processing_time);

            $order->delivery_time = $time_difference_in_min;
            $order->save();
        }
        
        try{
            OrderTransaction::insert([
                'vendor_id' =>$order->restaurant->vendor->id,
                'delivery_man_id'=>$order->delivery_man_id,
                'order_id' =>$order->id,
                'order_amount'=>$order->order_amount,
                'restaurant_amount'=>$order_amount + $order->total_tax_amount - $comission_amount,
                'admin_commission'=>$comission_amount,
                'delivery_charge'=>$order->delivery_charge,
                'original_delivery_charge'=>$order->original_delivery_charge,
                'tax'=>$order->total_tax_amount,
                'received_by'=> $received_by?$received_by:'admin',
                'zone_id'=>$order->zone_id,
                'status'=> $status,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $adminWallet = AdminWallet::firstOrNew(
                ['admin_id' => Admin::where('role_id', 1)->first()->id]
            );

            $vendorWallet = RestaurantWallet::firstOrNew(
                ['vendor_id' => $order->restaurant->vendor->id]
            );

            $adminWallet->total_commission_earning = $adminWallet->total_commission_earning+$comission_amount;

            if($order->restaurant->self_delivery_system)
            {
                $vendorWallet->total_earning = $vendorWallet->total_earning + $order->delivery_charge;
            }
            else{
                $adminWallet->delivery_charge = $adminWallet->delivery_charge+$order->delivery_charge;
            }
            

            $vendorWallet->total_earning = $vendorWallet->total_earning+($order_amount + $order->total_tax_amount - $comission_amount);
            try
            {
                DB::beginTransaction();
                if($received_by=='admin')
                {
                    $adminWallet->digital_received = $adminWallet->digital_received+$order->order_amount;
                }
                else if($received_by=='restaurant')
                {
                    $vendorWallet->collected_cash = $vendorWallet->collected_cash+$order->order_amount;
                }
                else if($received_by==false)
                {
                    $adminWallet->manual_received = $adminWallet->manual_received+$order->order_amount;
                }
                else if($received_by=='deliveryman' && $order->delivery_man->type == 'zone_wise')
                {
                    $dmWallet = DeliveryManWallet::firstOrNew(
                        ['delivery_man_id' => $order->delivery_man_id]
                    );
                    $dmWallet->collected_cash=$dmWallet->collected_cash+$order->order_amount;
                    $dmWallet->save();
                }
                // else if($order->restaurant->self_delivery_system)
                // {
                //     $vendorWallet->collected_cash = $vendorWallet->collected_cash+$order->order_amount - $order->delivery_charge;
                // }
                $adminWallet->save();
                $vendorWallet->save();
                DB::commit();
            }
            catch(\Exception $e)
            {
                DB::rollBack();
                info($e);
                return false;
            }
        }
        catch(\Exception $e){
            info($e);
            return false;
        }

        return true;
    }

    public static function refund_order($order)
    {
        $order_transaction = $order->transaction;
        if($order_transaction == null || $order->restaurant == null)
        {
            return false;
        }
        $received_by = $order_transaction->received_by;

        $adminWallet = AdminWallet::firstOrNew(
            ['admin_id' => Admin::where('role_id', 1)->first()->id]
        );

        $vendorWallet = RestaurantWallet::firstOrNew(
            ['vendor_id' => $order->restaurant->vendor->id]
        );

        
        $adminWallet->total_commission_earning = $adminWallet->total_commission_earning - $order_transaction->admin_commission;

        $vendorWallet->total_earning = $vendorWallet->total_earning - $order_transaction->restaurant_amount;

        $refund_amount = $order->order_amount;

        $status = 'refunded_with_delivery_charge';
        if($order->order_status == 'delivered')
        {
            $refund_amount = $order->order_amount - $order->delivery_charge;
            $status = 'refunded_without_delivery_charge';
        }
        else
        {
            $adminWallet->delivery_charge = $adminWallet->delivery_charge - $order_transaction->delivery_charge;
        }
        try
        {
            DB::beginTransaction();
            if($received_by=='admin')
            {
                if($order->delivery_man_id && $order->payment_method != "cash_on_delivery")
                {
                    $adminWallet->digital_received = $adminWallet->digital_received - $refund_amount;
                }
                else
                {
                    $adminWallet->manual_received = $adminWallet->manual_received - $refund_amount;
                }
                
            }
            else if($received_by=='restaurant')
            {
                $vendorWallet->collected_cash = $vendorWallet->collected_cash - $refund_amount;
            }

                // DB::table('account_transactions')->insert([
                //     'from_type'=>'customer',
                //     'from_id'=>$order->user_id,
                //     'current_balance'=> 0,
                //     'amount'=> $refund_amount,
                //     'method'=>'CASH',
                //     'created_at' => now(),
                //     'updated_at' => now()
                // ]);
 
            else if($received_by=='deliveryman')
            {
                $dmWallet = DeliveryManWallet::firstOrNew(
                    ['delivery_man_id' => $order->delivery_man_id]
                );
                $dmWallet->collected_cash=$dmWallet->collected_cash - $refund_amount;
                $dmWallet->save();
            }
            $order_transaction->status = $status;
            $order_transaction->save();
            $adminWallet->save();
            $vendorWallet->save();
            DB::commit();
        }
        catch(\Exception $e)
        {
            DB::rollBack();
            info($e);
            return false;
        }
        return true;

    }

    public static function update_order_status($group_id, $payment_method = null, $order_status = null, $payment_status = null, $transaction_reference = null, $delivery_man_id = null, $delivery_address = null)
    {
        $data = [];
        if (isset($payment_method)) $data['payment_method'] = $payment_method;
        if (isset($payment_status)) $data['payment_status'] = $payment_status;
        if (isset($order_status) && isset($delivery_man_id) && $order_status == 'accepted') $data['delivery_man_id'] = $delivery_man_id;
        if (isset($transaction_reference)) $data['transaction_reference'] = $transaction_reference;
        if (isset($delivery_address)) $data['delivery_address'] = $delivery_address;
        if (isset($order_status)) {
            $data[$order_status] = now();
            $data['order_status'] = $order_status;
            if($order_status == 'delivered') self::update_order_data($group_id);
        }

        try {
            Order::where('group_id', $group_id)->update($data);
        } catch (Exception $ex) {
            return $ex;
        }
        return true;
    }

    public static function update_order_data($group_id)
    {
        $orders = Order::with(['restaurant','customer','delivery_man','details'])->where('group_id', $group_id)->get();
        foreach($orders as $key=>$order){
            $order->details->each(function($item, $key){
                if($item->food)
                {
                    $item->food->increment('order_count');
                }
            });
            $order->restaurant->increment('order_count');
            if($key == 0)
            {
                $order->customer->increment('order_count');
            }
            else{
                $order->original_delivery_charge = 0;
                $order->delivery_charge = 0;
            }
            self::create_transaction($order);
        }
    }

    public static function order_log_create_or_update($dm_id,$or_id,$is_accept){

        $order_logs = OrderLog::where('delivery_man_id', $dm_id)->where('order_id', $or_id)->first();
        if ($order_logs) {
            $order_logs->delivery_man_id = $dm_id;
            $order_logs->order_id = $or_id;
            $order_logs->is_accept = $is_accept;
            return $order_logs->save();
        }
        
        $order_log = new OrderLog();
        $order_log->delivery_man_id = $dm_id;
        $order_log->order_id = $or_id;
        $order_log->is_accept = $is_accept;
        return $order_log->save();

    }
}
