<?php

namespace App\CentralLogics;

use App\Models\AddOn;
use App\Models\BusinessSetting;
use App\Models\Currency;
use App\Models\DMReview;
use App\Models\Order;
use App\Models\Review;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use App\CentralLogics\RestaurantLogic;
use App\Models\DeliveryMan;
use App\Models\DMTimeLog;
use App\Models\Shift;
use App\Models\SuspensionLog;
use App\Models\TimeLog;
use Carbon\Carbon as CarbonCarbon;
use DateTime;
use Illuminate\Support\Facades\DB;
use Laravelpkg\Laravelchk\Http\Controllers\LaravelchkController;

class Helpers
{
    public static function error_processor($validator)
    {
        $err_keeper = [];
        foreach ($validator->errors()->getMessages() as $index => $error) {
            array_push($err_keeper, ['code' => $index, 'message' => $error[0]]);
        }
        return $err_keeper;
    }

    public static function error_formater($key, $mesage, $errors = [])
    {
        $errors[] = ['code' => $key, 'message' => $mesage];

        return $errors;
    }

    public static function schedule_order()
    {
        return (boolean)BusinessSetting::where(['key' => 'schedule_order'])->first()->value;
    }


    public static function combinations($arrays)
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }
        return $result;
    }

    public static function variation_price($product, $variation)
    {
        $match = json_decode($variation, true)[0];
        $result = 0;
        foreach (json_decode($product['variations'], true) as $property => $value) {
            if ($value['type'] == $match['type']) {
                $result = $value['price'];
            }
        }
        return $result;
    }

    public static function format_deal_data($data, $multi_data = false)
    {
        $storage = [];
        if($multi_data){
            foreach($data as $item){
                $item = $item->toArray();
                foreach($item['options'] as $key=>$option){
                    if(isset($option['addon_ids'])){
                        $item['options'][$key]['addons'] = AddOn::whereIn('id', $option['addon_ids'])->get()->toArray();
                    }
                }
                $storage[] = $item;
            }
        } else {
            foreach($data['options'] as $key=>$option){
                if(isset($option['addon_ids'])){
                    $item['options'][$key]['addons'] = AddOn::whereIn('id', $option['addon_ids'])->get()->toArray();
                }
            }
            $storage = $data;
        }

        return $storage;
    }
    public static function product_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];
                if($item->title)
                {
                    $item['name']=$item->title;
                    unset($item['title']);
                }
                if($item->start_time)
                {
                    $item['available_time_starts']=$item->start_time->format('H:i');
                    unset($item['start_time']);
                }
                if($item->end_time)
                {
                    $item['available_time_ends']=$item->end_time->format('H:i');
                    unset($item['end_time']);
                }

                if($item->start_date)
                {
                    $item['available_date_starts']=$item->start_date->format('Y-m-d');
                    unset($item['start_date']);
                }
                if($item->end_date)
                {
                    $item['available_date_ends']=$item->end_date->format('Y-m-d');
                    unset($item['end_date']);
                }
                $categories = [];
                foreach(json_decode($item['category_ids']) as $value)
                {
                    $categories[] = ['id'=>(string)$value->id, 'position'=>$value->position];
                }
                $item['category_ids'] = $categories;
                // $item['category_ids'] = json_decode($item['category_ids']);
                $item['attributes'] = json_decode($item['attributes']);
                $item['choice_options'] = json_decode($item['choice_options']);
                $item['add_ons'] = AddOn::whereIn('id', json_decode($item['add_ons']))->active()->get();
                foreach (json_decode($item['variations'], true) as $var) {
                    array_push($variations, [
                        'type' => $var['type'],
                        'price' => (double)$var['price']
                    ]);
                }
                $item['variations'] = $variations;
                $item['restaurant_name'] = $item->restaurant->name;
                $item['restaurant_discount'] = self::get_restaurant_discount($item->restaurant)?$item->restaurant->discount->discount:0;
                $item['restaurant_opening_time'] = $item->restaurant->opening_time?$item->restaurant->opening_time->format('H:i'):null;
                $item['restaurant_closing_time'] = $item->restaurant->closeing_time?$item->restaurant->closeing_time->format('H:i'):null;
                $item['schedule_order'] = $item->restaurant->schedule_order;
                $item['tax'] = $item->restaurant->tax;
                $item['rating_count'] = (integer)($item->rating?array_sum(json_decode($item->rating, true)):0);
                $item['avg_rating'] = (double)($item->avg_rating?$item->avg_rating:0);
                unset($item['restaurant']);
                unset($item['rating']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            $variations = [];
            $categories = [];
            foreach(json_decode($data['category_ids']) as $value)
            {
                $categories[] = ['id'=>(string)$value->id, 'position'=>$value->position];
            }
            $data['category_ids'] = $categories;
            // $data['category_ids'] = json_decode($data['category_ids']);
            $data['attributes'] = json_decode($data['attributes']);
            $data['choice_options'] = json_decode($data['choice_options']);
            $data['add_ons'] = AddOn::whereIn('id', json_decode($data['add_ons']))->active()->get();
            foreach (json_decode($data['variations'], true) as $var) {
                array_push($variations, [
                    'type' => $var['type'],
                    'price' => (double)$var['price']
                ]);
            }
            if($data->title)
            {
                $data['name']=$data->title;
                unset($data['title']);
            }
            if($data->start_time)
            {
                $data['available_time_starts']=$data->start_time->format('H:i');
                unset($data['start_time']);
            }
            if($data->end_time)
            {
                $data['available_time_ends']=$data->end_time->format('H:i');
                unset($data['end_time']);
            }
            if($data->start_date)
            {
                $data['available_date_starts']=$data->start_date->format('Y-m-d');
                unset($data['start_date']);
            }
            if($data->end_date)
            {
                $data['available_date_ends']=$data->end_date->format('Y-m-d');
                unset($data['end_date']);
            }
            $data['variations'] = $variations;
            $data['restaurant_name'] = $data->restaurant->name;
            $data['restaurant_discount'] = self::get_restaurant_discount($data->restaurant)?$data->restaurant->discount->discount:0;
            $data['restaurant_opening_time'] = $data->restaurant->opening_time?$data->restaurant->opening_time->format('H:i'):null;
            $data['restaurant_closing_time'] = $data->restaurant->closeing_time?$data->restaurant->closeing_time->format('H:i'):null;
            $data['schedule_order'] = $data->restaurant->schedule_order;
            $data['rating_count'] = (integer)($data->rating?array_sum(json_decode($data->rating, true)):0);
            $data['avg_rating'] = (double)($data->avg_rating?$data->avg_rating:0);
            unset($data['restaurant']);
            unset($data['rating']);
        }

        return $data;
    }

    public static function basic_campaign_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                $variations = [];

                if($item->start_date)
                {
                    $item['available_date_starts']=$item->start_date->format('Y-m-d');
                    unset($item['start_date']);
                }
                if($item->end_date)
                {
                    $item['available_date_ends']=$item->end_date->format('Y-m-d');
                    unset($item['end_date']);
                }
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            if($data->start_date)
            {
                $data['available_date_starts']=$data->start_date->format('Y-m-d');
                unset($data['start_date']);
            }
            if($data->end_date)
            {
                $data['available_date_ends']=$data->end_date->format('Y-m-d');
                unset($data['end_date']);
            }
        }

        return $data;
    }
    public static function restaurant_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if ($multi_data == true) {
            foreach ($data as $item) {
                if($item->opening_time)
                {
                    $item['available_time_starts']=$item->opening_time->format('H:i');
                    unset($item['opening_time']);
                }
                if($item->closeing_time)
                {
                    $item['available_time_ends']=$item->closeing_time->format('H:i');
                    unset($item['closeing_time']);
                }

                $ratings=RestaurantLogic::calculate_restaurant_rating($item['rating']);
                unset($item['rating']);
                $item['avg_rating']=$ratings['rating'];
                $item['rating_count ']=$ratings['total'];
                unset($item['campaigns']);
                unset($item['pivot']);
                array_push($storage, $item);
            }
            $data = $storage;
        } else {
            if($data->opening_time)
            {
                $data['available_time_starts']=$data->opening_time->format('H:i');
                unset($data['opening_time']);
            }
            if($data->closeing_time)
            {
                $data['available_time_ends']=$data->closeing_time->format('H:i');
                unset($data['closeing_time']);
            }
            $ratings=RestaurantLogic::calculate_restaurant_rating($data['rating']);
            unset($data['rating']);
            $data['avg_rating']=$ratings['rating'];
            $data['rating_count ']=$ratings['total'];
            unset($data['campaigns']);
            unset($data['pivot']);
        }

        return $data;
    }

    public static function wishlist_data_formatting($data, $multi_data = false)
    {
        $foods = [];
        $restaurants = [];
        if ($multi_data == true) {

            foreach ($data as $item) {
                if($item->food)
                {
                    $foods[] = self::product_data_formatting($item->food);
                }
                if($item->restaurant)
                {
                    $restaurants[] = self::restaurant_data_formatting($item->restaurant);
                }
            }
        } else {
            if($data->food)
            {
                $foods[] = self::product_data_formatting($data->food);
            }
            if($data->restaurant)
            {
                $restaurants[] = self::restaurant_data_formatting($data->restaurant);
            }
        }

        return ['food'=>$foods, 'restaurant'=>$restaurants];
    }

    public static function order_data_formatting($data, $multi_data = false)
    {
        $storage = [];
        if($multi_data)
        {
            foreach ($data as $item) {
                if(isset($item['restaurant']))
                {
                    $item['restaurant_name'] = $item['restaurant']['name'];
                    $item['restaurant_address'] = $item['restaurant']['address'];
                    $item['restaurant_phone'] = $item['restaurant']['phone'];
                    $item['restaurant_lat'] = $item['restaurant']['latitude'];
                    $item['restaurant_lng'] = $item['restaurant']['longitude'];
                    $item['restaurant_logo'] = $item['restaurant']['logo'];
                    unset($item['restaurant']);
                }
                else
                {
                    $item['restaurant_name'] = null;
                    $item['restaurant_address'] = null;
                    $item['restaurant_phone'] = null;
                    $item['restaurant_lat'] = null;
                    $item['restaurant_lng'] = null;
                    $item['restaurant_logo'] = null;
                }
                $item['food_campaign'] = 0;
                foreach($item->details as $d)
                {
                    if($d->item_campaign_id != null)
                    {
                        $item['food_campaign'] = 1;
                    }
                }

                $item['delivery_address'] = $item->delivery_address?json_decode($item->delivery_address, true): null;
                $item['details_count'] = (integer)$item->details->count();
                unset($item['details']);
                array_push($storage, $item);
            }
            $data = $storage;
        }
        else
        {
            if(isset($data['restaurant']))
            {
                $data['restaurant_name'] = $data['restaurant']['name'];
                $data['restaurant_address'] = $data['restaurant']['address'];
                $data['restaurant_phone'] = $data['restaurant']['phone'];
                $data['restaurant_lat'] = $data['restaurant']['latitude'];
                $data['restaurant_lng'] = $data['restaurant']['longitude'];
                $data['restaurant_logo'] = $data['restaurant']['logo'];
                unset($data['restaurant']);
            }
            else
            {
                $data['restaurant_name'] = null;
                $data['restaurant_address'] = null;
                $data['restaurant_phone'] = null;
                $data['restaurant_lat'] = null;
                $data['restaurant_lng'] = null;
                $data['restaurant_logo'] = null;
            }

            $data['food_campaign'] = 0;
            foreach($data->details as $d)
            {
                if($d->item_campaign_id != null)
                {
                    $data['food_campaign'] = 1;
                }
            }
            $data['delivery_address'] = $data->delivery_address?json_decode($data->delivery_address, true): null;
            $data['details_count'] = (integer)$data->details->count();
            unset($data['details']);
        }
        return $data;
    }

    public static function order_details_data_formatting($data)
    {
        $storage = [];
        foreach ($data as $item) {
            if(isset($item['deal_id'])){
                array_push($storage, json_decode($item['food_details'], true));
            }else{
                $item['add_ons'] = json_decode($item['add_ons']);
                $item['variation'] = json_decode($item['variation']);
                $item['food_details'] = json_decode($item['food_details'], true);
                array_push($storage, $item);
            }
        }
        $data = $storage;
        return $data;
    }

    public static function deliverymen_list_formatting($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $storage[]=[
                'id'=>$item['id'],
                'name'=>$item['f_name'].' '.$item['l_name'],
                'image'=>$item['image'],
                'lat'=>$item->last_location?$item->last_location->latitude:false,
                'lng'=>$item->last_location?$item->last_location->longitude:false,
                'location'=>$item->last_location?$item->last_location->location:'',
                'vehicle' => $item?->laundry_vehicle_type?->name
            ];
        }
        $data = $storage;

        return $data;
    }

    public static function deliverymen_data_formatting($data)
    {
        $storage = [];
        foreach ($data as $item) {
            $item['avg_rating']=(float)(count($item->rating)?(float)$item->rating[0]->average:0);
            $item['rating_count']=(integer)(count($item->rating)?$item->rating[0]->rating_count:0);
            $item['lat']=$item->last_location?$item->last_location->latitude:null;
            $item['lng']=$item->last_location?$item->last_location->longitude:null;
            $item['location']=$item->last_location?$item->last_location->location:null;
            if($item['rating'])
            {
                unset($item['rating']);
            }
            if($item['last_location'])
            {
                unset($item['last_location']);
            }
            $storage[]=$item;
        }
        $data = $storage;

        return $data;
    }

    public static function get_business_settings($name)
    {
        $config = null;

        $paymentmethod = BusinessSetting::where('key', $name)->first();

        if ($paymentmethod) {
            $config = json_decode($paymentmethod->value, true);
        }

        return $config;
    }

    public static function currency_code()
    {
        return BusinessSetting::where(['key' => 'currency'])->first()->value;
    }

    public static function currency_symbol()
    {
        $currency_symbol = Currency::where(['currency_code' => Helpers::currency_code()])->first()->currency_symbol;
        return $currency_symbol;
    }

    public static function format_currency($value)
    {
        $currency_symbol_position = BusinessSetting::where(['key' => 'currency_symbol_position'])->first()->value;

        return $currency_symbol_position=='right'?$value.' '.self::currency_symbol():self::currency_symbol().' '.$value;
    }
    public static function send_push_notif_to_device($fcm_token, $data)
    {
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;
        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );

        $postdata = '{
            "to" : "' . $fcm_token . '",
            "mutable_content": true,
            "data" : {
                "title":"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0
            },
            "notification" : {
                "title" :"' . $data['title'] . '",
                "body" : "' . $data['description'] . '",
                "image" : "' . $data['image'] . '",
                "order_id":"' . $data['order_id'] . '",
                "title_loc_key":"' . $data['order_id'] . '",
                "body_loc_key":"' . $data['type'] . '",
                "type":"' . $data['type'] . '",
                "is_read": 0,
                "icon" : "new",
                "sound" : "notification.wav"
            }
        }';
        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function send_push_notif_to_topic($data, $topic, $type)
    {
        $key = BusinessSetting::where(['key' => 'push_notification_key'])->first()->value;

        $url = "https://fcm.googleapis.com/fcm/send";
        $header = array("authorization: key=" . $key . "",
            "content-type: application/json"
        );
        if(isset($data['order_id']))
        {
            $postdata = '{
                "to" : "/topics/'.$topic.'",
                "mutable_content": true,
                "data" : {
                    "title":"' . $data['title'] . '",
                    "body" : "' . $data['description'] . '",
                    "image" : "' . $data['image'] . '",
                    "order_id":"' . $data['order_id'] . '",
                    "is_read": 0,
                    "type":"'.$type.'"
                },
                "notification" : {
                    "title":"' . $data['title'] . '",
                    "body" : "' . $data['description'] . '",
                    "image" : "' . $data['image'] . '",
                    "order_id":"' . $data['order_id'] . '",
                    "title_loc_key":"' . $data['order_id'] . '",
                    "body_loc_key":"' . $type . '",
                    "type":"'.$type.'",
                    "is_read": 0,
                    "icon" : "new",
                    "sound": "notification.wav"
                  }
            }';
        }
        else{
            $postdata = '{
                "to" : "/topics/'.$topic.'",
                "mutable_content": true,
                "data" : {
                    "title":"' . $data['title'] . '",
                    "body" : "' . $data['description'] . '",
                    "image" : "' . $data['image'] . '",
                    "is_read": 0,
                    "type":"'.$type.'"
                },
                "notification" : {
                    "title":"' . $data['title'] . '",
                    "body" : "' . $data['description'] . '",
                    "image" : "' . $data['image'] . '",
                    "body_loc_key":"' . $type . '",
                    "type":"'.$type.'",
                    "is_read": 0,
                    "icon" : "new",
                    "sound": "notification.wav"
                  }
            }';
        }


        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        // close handle to release resources
        curl_close($ch);

        return $result;
    }

    public static function rating_count($food_id, $rating)
    {
        return Review::where(['food_id' => $food_id, 'rating' => $rating])->count();
    }

    public static function dm_rating_count($deliveryman_id, $rating)
    {
        return DMReview::where(['delivery_man_id' => $deliveryman_id, 'rating' => $rating])->count();
    }

    public static function tax_calculate($food, $price)
    {
        if ($food['tax_type'] == 'percent') {
            $price_tax = ($price / 100) * $food['tax'];
        } else {
            $price_tax = $food['tax'];
        }
        return $price_tax;
    }

    public static function discount_calculate($product, $price)
    {
        if ($product['restaurant_discount']) {
            $price_discount = ($price / 100) * $product['restaurant_discount'];
        }
        else if($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    public static function get_product_discount($product)
    {
        $restaurant_discount = self::get_restaurant_discount($product->restaurant);
        if ($restaurant_discount) {
            $discount = $restaurant_discount['discount'].' %';
        }
        else if($product['discount_type'] == 'percent') {
            $discount = $product['discount'].' %';
        } else {
            $discount = self::format_currency($product['discount']);
        }
        return $discount;
    }

    public static function product_discount_calculate($product, $price, $restaurant)
    {
        $restaurant_discount = self::get_restaurant_discount($restaurant);
        if(isset($restaurant_discount))
        {
            $price_discount = ($price / 100) * $restaurant_discount['discount'];
        }
        else if($product['discount_type'] == 'percent') {
            $price_discount = ($price / 100) * $product['discount'];
        } else {
            $price_discount = $product['discount'];
        }
        return $price_discount;
    }

    public static function get_price_range($product , $discount=false)
    {
        $lowest_price = $product->price;
        $highest_price = $product->price;

        foreach (json_decode($product->variations) as $key => $variation) {
            if ($lowest_price > $variation->price) {
                $lowest_price = round($variation->price, 2);
            }
            if ($highest_price < $variation->price) {
                $highest_price = round($variation->price, 2);
            }
        }
        if($discount)
        {
            $lowest_price -= self::product_discount_calculate($product, $lowest_price, $product->restaurant);
            $highest_price -= self::product_discount_calculate($product, $highest_price, $product->restaurant);
        }
        $lowest_price = self::format_currency($lowest_price);
        $highest_price = self::format_currency($highest_price);

        if ($lowest_price == $highest_price) {
            return $lowest_price;
        }
        return $lowest_price . ' - ' . $highest_price;
    }

    public static function get_restaurant_discount($restaurant)
    {
        if($restaurant->discount)
        {
            if(date('Y-m-d', strtotime($restaurant->discount->start_date)) <= now()->format('Y-m-d') && date('Y-m-d',strtotime($restaurant->discount->end_date)) >= now()->format('Y-m-d') && date('H:i', strtotime($restaurant->discount->start_time)) <= now()->format('H:i') && date('H:i', strtotime($restaurant->discount->end_time)) >= now()->format('H:i'))
            {
                return [
                    'discount'=>$restaurant->discount->discount,
                    'min_purchase'=>$restaurant->discount->min_purchase,
                    'max_discount'=>$restaurant->discount->max_discount
                ];
            }
        }
        return null;
    }

    public static function max_earning()
    {
        $data = Order::where(['order_status' => 'delivered'])->select('id', 'created_at', 'order_amount')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += $order['order_amount'];
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function max_orders()
    {
        $data = Order::select('id', 'created_at')
            ->get()
            ->groupBy(function ($date) {
                return Carbon::parse($date->created_at)->format('m');
            });

        $max = 0;
        foreach ($data as $month) {
            $count = 0;
            foreach ($month as $order) {
                $count += 1;
            }
            if ($count > $max) {
                $max = $count;
            }
        }
        return $max;
    }

    public static function order_status_update_message($status)
    {
        if ($status == 'pending') {
            $data = BusinessSetting::where('key', 'order_pending_message')->first()->value;
        } elseif ($status == 'confirmed') {
            $data = BusinessSetting::where('key', 'order_confirmation_msg')->first()->value;
        } elseif ($status == 'processing') {
            $data = BusinessSetting::where('key', 'order_processing_message')->first()->value;
        } elseif ($status == 'picked_up') {
            $data = BusinessSetting::where('key', 'out_for_delivery_message')->first()->value;
        } elseif ($status == 'handover') {
            $data = BusinessSetting::where('key', 'order_handover_message')->first()->value;
        } elseif ($status == 'delivered') {
            $data = BusinessSetting::where('key', 'order_delivered_message')->first()->value;
        }
        elseif ($status == 'delivery_boy_delivered') {
            $data = BusinessSetting::where('key', 'delivery_boy_delivered_message')->first()->value;
        }
        elseif ($status == 'accepted') {
            $data = BusinessSetting::where('key', 'delivery_boy_assign_message')->first()->value;
        }
        elseif ($status == 'canceled') {
            $data = BusinessSetting::where('key', 'order_cancled_message')->first()->value;
        }
        elseif ($status == 'refunded') {
            $data = BusinessSetting::where('key', 'order_refunded_message')->first()->value;
        }
        else {
            $data = '{"status":"0","message":""}';
        }

        $res = json_decode($data, true);

        if ($res['status'] == 0) {
            return 0;
        }
        return $res['message'];
    }

    public static function send_order_notification($order, $changed_by = 'any')
    {

        try {
            $status = ($order->order_status == 'delivered' && $order->delivery_man)?'delivery_boy_delivered':$order->order_status;
            $value = ($status == 'canceled' && $changed_by == 'restaurant') ? trans('messages.order_canceled_by_restaurant') : self::order_status_update_message($status);
            if ($value) {
                $data = [
                    'title' =>trans('messages.order_push_title'),
                    'description' => $value,
                    'order_id' => $order->id,
                    'image' => '',
                    'type'=>'order_status',
                ];
                self::send_push_notif_to_device($order->customer->cm_firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data'=> json_encode($data),
                    'user_id'=>$order->user_id,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ]);
            }

            if($status == 'picked_up')
            {
                $data = [
                    'title' =>trans('messages.order_push_title'),
                    'description' => $value,
                    'order_id' => $order->id,
                    'image' => '',
                    'type'=>'order_status',
                ];
                self::send_push_notif_to_device($order->restaurant->vendor->firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data'=> json_encode($data),
                    'vendor_id'=>$order->restaurant->vendor_id,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ]);
            }

            if($order->order_type == 'delivery' && !$order->scheduled && $order->order_status == 'pending' && $order->payment_method == 'cash_on_delivery' && config('order_confirmation_model') == 'deliveryman' && $order->order_type != 'take_away')
            {
                if($order->restaurant->self_delivery_system)
                {
                    $data = [
                        'title' =>trans('messages.order_push_title'),
                        'description' => trans('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'image' => '',
                        'type'=>'new_order',
                    ];
                    self::send_push_notif_to_device($order->restaurant->vendor->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data'=> json_encode($data),
                        'vendor_id'=>$order->restaurant->vendor_id,
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ]);
                }
                else
                {
                    $data = [
                        'title' =>trans('messages.order_push_title'),
                        'description' => trans('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'image' => '',
                    ];
                    self::send_push_notif_to_topic($data, $order->restaurant->zone->deliveryman_wise_topic, 'order_request');
                }
            }

            if($order->order_type == 'delivery' && !$order->scheduled && $order->order_status == 'pending' && $order->payment_method == 'cash_on_delivery' && config('order_confirmation_model') == 'restaurant')
            {
                $data = [
                    'title' =>trans('messages.order_push_title'),
                    'description' => trans('messages.new_order_push_description'),
                    'order_id' => $order->id,
                    'image' => '',
                    'type'=>'new_order',
                ];
                self::send_push_notif_to_device($order->restaurant->vendor->firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data'=> json_encode($data),
                    'vendor_id'=>$order->restaurant->vendor_id,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ]);
            }

            if(!$order->scheduled && (($order->order_type == 'take_away' && $order->order_status == 'pending') || ($order->payment_method != 'cash_on_delivery' && $order->order_status == 'confirmed')))
            {
                $data = [
                    'title' =>trans('messages.order_push_title'),
                    'description' => trans('messages.new_order_push_description'),
                    'order_id' => $order->id,
                    'image' => '',
                    'type'=>'new_order',
                ];
                self::send_push_notif_to_device($order->restaurant->vendor->firebase_token, $data);
                DB::table('user_notifications')->insert([
                    'data'=> json_encode($data),
                    'vendor_id'=>$order->restaurant->vendor_id,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ]);
            }

            if($order->order_status == 'confirmed' && $order->order_type != 'take_away' && config('order_confirmation_model') == 'deliveryman' && $order->payment_method == 'cash_on_delivery')
            {
                if($order->restaurant->self_delivery_system)
                {
                    $data = [
                        'title' =>trans('messages.order_push_title'),
                        'description' => trans('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'image' => '',
                    ];

                    self::send_push_notif_to_topic($data, "restaurant_dm_".$order->restaurant_id, 'new_order');
                }
                else
                {
                    $data = [
                        'title' =>trans('messages.order_push_title'),
                        'description' => trans('messages.new_order_push_description'),
                        'order_id' => $order->id,
                        'image' => '',
                        'type'=>'new_order',
                    ];
                    self::send_push_notif_to_device($order->restaurant->vendor->firebase_token, $data);
                    DB::table('user_notifications')->insert([
                        'data'=> json_encode($data),
                        'vendor_id'=>$order->restaurant->vendor_id,
                        'created_at'=>now(),
                        'updated_at'=>now()
                    ]);
                }
            }

            if($order->order_type == 'delivery' && !$order->scheduled && $order->order_status == 'confirmed'  && ($order->payment_method != 'cash_on_delivery' || config('order_confirmation_model') == 'restaurant'))
            {
                $data = [
                    'title' =>trans('messages.order_push_title'),
                    'description' => trans('messages.new_order_push_description'),
                    'order_id' => $order->id,
                    'image' => '',
                ];
                if($order->restaurant->self_delivery_system)
                {
                    self::send_push_notif_to_topic($data, "restaurant_dm_".$order->restaurant_id, 'order_request');
                }
                else{
                    self::send_push_notif_to_topic($data, $order->restaurant->zone->deliveryman_wise_topic, 'order_request');
                }
            }

            if(in_array($order->order_status, ['processing', 'handover']) && $order->delivery_man)
            {
                $data = [
                    'title' =>trans('messages.order_push_title'),
                    'description' => $order->order_status=='processing'?trans('messages.Proceed_for_cooking'):trans('messages.ready_for_delivery'),
                    'order_id' => $order->id,
                    'image' => '',
                    'type'=>'order_status'
                ];
                self::send_push_notif_to_device($order->delivery_man->fcm_token, $data);
                DB::table('user_notifications')->insert([
                    'data'=> json_encode($data),
                    'delivery_man_id'=>$order->delivery_man->id,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ]);
            }
            return true;

        } catch (\Exception $e) {
            info($e);
        }
        return false;
    }

    public static function day_part()
    {
        $part = "";
        $morning_start = date("h:i:s", strtotime("5:00:00"));
        $afternoon_start = date("h:i:s", strtotime("12:01:00"));
        $evening_start = date("h:i:s", strtotime("17:01:00"));
        $evening_end = date("h:i:s", strtotime("21:00:00"));

        if (time() >= $morning_start && time() < $afternoon_start) {
            $part = "morning";
        } elseif (time() >= $afternoon_start && time() < $evening_start) {
            $part = "afternoon";
        } elseif (time() >= $evening_start && time() <= $evening_end) {
            $part = "evening";
        } else {
            $part = "night";
        }

        return $part;
    }

    public static function env_update($key,$value){
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key.'='.env($key), $key.'='.$value, file_get_contents($path)
            ));
        }
    }

    public static function env_key_replace($key_from,$key_to,$value){
        $path = base_path('.env');
        if (file_exists($path)) {
            file_put_contents($path, str_replace(
                $key_from.'='.env($key_from), $key_to.'='.$value, file_get_contents($path)
            ));
        }
    }

    public static  function remove_dir($dir) {
        if (is_dir($dir)) {
            $objects = scandir($dir);
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir."/".$object) == "dir") Helpers::remove_dir($dir."/".$object); else unlink($dir."/".$object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    public static function get_restaurant_id()
    {
        if(auth('vendor_employee')->check())
        {
            return auth('vendor_employee')->user()->restaurant->id;
        }
        return auth('vendor')->user()->restaurants[0]->id;
    }

    public static function get_vendor_id()
    {
        if(auth('vendor')->check())
        {
            return auth('vendor')->id();
        }
        else if(auth('vendor_employee')->check())
        {
            return auth('vendor_employee')->user()->vendor_id;
        }
        return 0;
    }

    public static function get_vendor_data()
    {
        if(auth('vendor')->check())
        {
            return auth('vendor')->user();
        }
        else if(auth('vendor_employee')->check())
        {
            return auth('vendor_employee')->user()->vendor;
        }
        return 0;
    }

    public static function get_loggedin_user()
    {
        if(auth('vendor')->check())
        {
            return auth('vendor')->user();
        }
        else if(auth('vendor_employee')->check())
        {
            return auth('vendor_employee')->user();
        }
        return 0;
    }

    public static function get_restaurant_data()
    {
        if(auth('vendor_employee')->check())
        {
            return auth('vendor_employee')->user()->restaurant;
        }
        return auth('vendor')->user()->restaurants[0];
    }

    public static function upload(string $dir, string $format, $image = null, $imageName = null)
    {
        if ($image != null) {
            $imageName = isset($imageName)?$imageName:\Carbon\Carbon::now()->toDateString() . "-" . uniqid();
            $imageName .=  "." . $format;
            if (!Storage::disk('public')->exists($dir)) {
                Storage::disk('public')->makeDirectory($dir);
            }
            Storage::disk('public')->put($dir . $imageName, file_get_contents($image));
        } else {
            $imageName = 'def.png';
        }

        return $imageName;
    }

    public static function update(string $dir, $old_image, string $format, $image = null)
    {
        if($image == null)
        {
            return $old_image;
        }
        if (Storage::disk('public')->exists($dir . $old_image)) {
            Storage::disk('public')->delete($dir . $old_image);
        }
        $imageName = Helpers::upload($dir, $format, $image);
        return $imageName;
    }

    public static function format_coordiantes($coordinates)
    {
        $data = [];
        foreach($coordinates as $coord)
        {
            $data[] = (object)['lat'=>$coord->getlat(), 'lng'=>$coord->getlng()];
        }
        return $data;
    }

    public static function module_permission_check($mod_name)
    {
        if(!auth('admin')->user()->role)
        {
            return false;
        }

        if($mod_name == 'zone' && auth('admin')->user()->zone_id)
        {
            return false;
        }

        $permission = auth('admin')->user()->role->modules;
        if (isset($permission) && in_array($mod_name, (array)json_decode($permission)) == true) {
            return true;
        }

        if (auth('admin')->user()->role_id == 1) {
            return true;
        }
        return false;
    }

    public static function employee_module_permission_check($mod_name)
    {
        if (auth('vendor')->check()) {
            if($mod_name == 'reviews')
            {
                return auth('vendor')->user()->restaurants[0]->reviews_section;
            }
            else if($mod_name == 'deliveryman')
            {
                return auth('vendor')->user()->restaurants[0]->self_delivery_system;
            }
            else if($mod_name == 'pos')
            {
                return auth('vendor')->user()->restaurants[0]->pos_system;
            }
            return true;
        }
        else if(auth('vendor_employee')->check())
        {
            $permission = auth('vendor_employee')->user()->role->modules;
            if (isset($permission) && in_array($mod_name, (array)json_decode($permission)) == true) {
                if($mod_name == 'reviews')
                {
                    return auth('vendor_employee')->user()->restaurant->reviews_section;
                }
                else if($mod_name == 'deliveryman')
                {
                    return auth('vendor_employee')->user()->restaurant->self_delivery_system;
                }

                else if($mod_name == 'pos')
                {
                    return auth('vendor_employee')->user()->restaurant->pos_system;
                }
                return true;
            }
        }

        return false;
    }
    public static function calculate_addon_price($addons,$add_on_qtys)
    {
        $add_ons_cost = 0;
        $data = [];
        if($addons)
        {
            foreach($addons as $key2 =>$addon)
            {
                if($add_on_qtys==null)
                {
                    $add_on_qty=1;
                }
                else
                {
                    $add_on_qty=$add_on_qtys[$key2];
                }
                $data[] = ['id'=>$addon->id,'name'=>$addon->name, 'price'=>$addon->price, 'quantity'=> $add_on_qty];
                $add_ons_cost+=$addon['price']*$add_on_qty;
            }
            return ['addons'=> $data, 'total_add_on_price'=>$add_ons_cost];
        }
        return null;
    }

    public static function calculate_deal_addon_price($options, $quantity=1)
    {
        $add_ons_cost = 0;
        $data = [];
        foreach($options as $option){
            if(isset($option['addons'])){
                foreach($option['addons'] as $addon){
                    $data[] = ['id'=>$addon['id'],'name'=>$addon['name'], 'price'=>$addon['price'], 'quantity'=> $quantity];
                    $add_ons_cost+=$addon['price']*$quantity;
                }
            }
        }
        return ['addons'=> $data, 'total_add_on_price'=>$add_ons_cost];
    }
    public static function get_settings($name)
    {
        $config = null;
        $data = BusinessSetting::where(['key' => $name])->first();
        if (isset($data)) {
            $config = json_decode($data['value'], true);
            if (is_null($config)) {
                $config = $data['value'];
            }
        }
        return $config;
    }

    public static function setEnvironmentValue($envKey, $envValue)
    {
        $envFile = app()->environmentFilePath();
        $str = file_get_contents($envFile);
        $oldValue = env($envKey);
        if (strpos($str, $envKey) !== false) {
            $str = str_replace("{$envKey}={$oldValue}", "{$envKey}={$envValue}", $str);
        } else {
            $str .= "{$envKey}={$envValue}\n";
        }
        $fp = fopen($envFile, 'w');
        fwrite($fp, $str);
        fclose($fp);
        return $envValue;
    }

    // public static function requestSender()
    // {
    //     $client = new \GuzzleHttp\Client();
    //     $response = $client->get(route(base64_decode('YWN0aXZhdGlvbi1jaGVjaw==')));
    //     $data = json_decode($response->getBody()->getContents(), true);
    //     return $data;
    // }
    public static function requestSender()
    {
        $class = new LaravelchkController();
        $response = $class->actch();
        return json_decode($response->getContent(),true);
    }


    public static function insert_business_settings_key($key, $value=null)
    {
        $data =  BusinessSetting::where('key', $key)->first();
        if(!$data)
        {
            DB::table('business_settings')->updateOrInsert(['key' => $key], [
                'value' => $value,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
        return true;
    }

    public static function get_language_name($key)
    {
        $languages = array(
            "af" => "Afrikaans",
            "sq" => "Albanian - shqip",
            "am" => "Amharic - አማርኛ",
            "ar" => "Arabic - العربية",
            "an" => "Aragonese - aragonés",
            "hy" => "Armenian - հայերեն",
            "ast" => "Asturian - asturianu",
            "az" => "Azerbaijani - azərbaycan dili",
            "eu" => "Basque - euskara",
            "be" => "Belarusian - беларуская",
            "bn" => "Bengali - বাংলা",
            "bs" => "Bosnian - bosanski",
            "br" => "Breton - brezhoneg",
            "bg" => "Bulgarian - български",
            "ca" => "Catalan - català",
            "ckb" => "Central Kurdish - کوردی (دەستنوسی عەرەبی)",
            "zh" => "Chinese - 中文",
            "zh-HK" => "Chinese (Hong Kong) - 中文（香港）",
            "zh-CN" => "Chinese (Simplified) - 中文（简体）",
            "zh-TW" => "Chinese (Traditional) - 中文（繁體）",
            "co" => "Corsican",
            "hr" => "Croatian - hrvatski",
            "cs" => "Czech - čeština",
            "da" => "Danish - dansk",
            "nl" => "Dutch - Nederlands",
            "en" => "English",
            "en-AU" => "English (Australia)",
            "en-CA" => "English (Canada)",
            "en-IN" => "English (India)",
            "en-NZ" => "English (New Zealand)",
            "en-ZA" => "English (South Africa)",
            "en-GB" => "English (United Kingdom)",
            "en-US" => "English (United States)",
            "eo" => "Esperanto - esperanto",
            "et" => "Estonian - eesti",
            "fo" => "Faroese - føroyskt",
            "fil" => "Filipino",
            "fi" => "Finnish - suomi",
            "fr" => "French - français",
            "fr-CA" => "French (Canada) - français (Canada)",
            "fr-FR" => "French (France) - français (France)",
            "fr-CH" => "French (Switzerland) - français (Suisse)",
            "gl" => "Galician - galego",
            "ka" => "Georgian - ქართული",
            "de" => "German - Deutsch",
            "de-AT" => "German (Austria) - Deutsch (Österreich)",
            "de-DE" => "German (Germany) - Deutsch (Deutschland)",
            "de-LI" => "German (Liechtenstein) - Deutsch (Liechtenstein)",
            "de-CH" => "German (Switzerland) - Deutsch (Schweiz)",
            "el" => "Greek - Ελληνικά",
            "gn" => "Guarani",
            "gu" => "Gujarati - ગુજરાતી",
            "ha" => "Hausa",
            "haw" => "Hawaiian - ʻŌlelo Hawaiʻi",
            "he" => "Hebrew - עברית",
            "hi" => "Hindi - हिन्दी",
            "hu" => "Hungarian - magyar",
            "is" => "Icelandic - íslenska",
            "id" => "Indonesian - Indonesia",
            "ia" => "Interlingua",
            "ga" => "Irish - Gaeilge",
            "it" => "Italian - italiano",
            "it-IT" => "Italian (Italy) - italiano (Italia)",
            "it-CH" => "Italian (Switzerland) - italiano (Svizzera)",
            "ja" => "Japanese - 日本語",
            "kn" => "Kannada - ಕನ್ನಡ",
            "kk" => "Kazakh - қазақ тілі",
            "km" => "Khmer - ខ្មែរ",
            "ko" => "Korean - 한국어",
            "ku" => "Kurdish - Kurdî",
            "ky" => "Kyrgyz - кыргызча",
            "lo" => "Lao - ລາວ",
            "la" => "Latin",
            "lv" => "Latvian - latviešu",
            "ln" => "Lingala - lingála",
            "lt" => "Lithuanian - lietuvių",
            "mk" => "Macedonian - македонски",
            "ms" => "Malay - Bahasa Melayu",
            "ml" => "Malayalam - മലയാളം",
            "mt" => "Maltese - Malti",
            "mr" => "Marathi - मराठी",
            "mn" => "Mongolian - монгол",
            "ne" => "Nepali - नेपाली",
            "no" => "Norwegian - norsk",
            "nb" => "Norwegian Bokmål - norsk bokmål",
            "nn" => "Norwegian Nynorsk - nynorsk",
            "oc" => "Occitan",
            "or" => "Oriya - ଓଡ଼ିଆ",
            "om" => "Oromo - Oromoo",
            "ps" => "Pashto - پښتو",
            "fa" => "Persian - فارسی",
            "pl" => "Polish - polski",
            "pt" => "Portuguese - português",
            "pt-BR" => "Portuguese (Brazil) - português (Brasil)",
            "pt-PT" => "Portuguese (Portugal) - português (Portugal)",
            "pa" => "Punjabi - ਪੰਜਾਬੀ",
            "qu" => "Quechua",
            "ro" => "Romanian - română",
            "mo" => "Romanian (Moldova) - română (Moldova)",
            "rm" => "Romansh - rumantsch",
            "ru" => "Russian - русский",
            "gd" => "Scottish Gaelic",
            "sr" => "Serbian - српски",
            "sh" => "Serbo-Croatian - Srpskohrvatski",
            "sn" => "Shona - chiShona",
            "sd" => "Sindhi",
            "si" => "Sinhala - සිංහල",
            "sk" => "Slovak - slovenčina",
            "sl" => "Slovenian - slovenščina",
            "so" => "Somali - Soomaali",
            "st" => "Southern Sotho",
            "es" => "Spanish - español",
            "es-AR" => "Spanish (Argentina) - español (Argentina)",
            "es-419" => "Spanish (Latin America) - español (Latinoamérica)",
            "es-MX" => "Spanish (Mexico) - español (México)",
            "es-ES" => "Spanish (Spain) - español (España)",
            "es-US" => "Spanish (United States) - español (Estados Unidos)",
            "su" => "Sundanese",
            "sw" => "Swahili - Kiswahili",
            "sv" => "Swedish - svenska",
            "tg" => "Tajik - тоҷикӣ",
            "ta" => "Tamil - தமிழ்",
            "tt" => "Tatar",
            "te" => "Telugu - తెలుగు",
            "th" => "Thai - ไทย",
            "ti" => "Tigrinya - ትግርኛ",
            "to" => "Tongan - lea fakatonga",
            "tr" => "Turkish - Türkçe",
            "tk" => "Turkmen",
            "tw" => "Twi",
            "uk" => "Ukrainian - українська",
            "ur" => "Urdu - اردو",
            "ug" => "Uyghur",
            "uz" => "Uzbek - o‘zbek",
            "vi" => "Vietnamese - Tiếng Việt",
            "wa" => "Walloon - wa",
            "cy" => "Welsh - Cymraeg",
            "fy" => "Western Frisian",
            "xh" => "Xhosa",
            "yi" => "Yiddish",
            "yo" => "Yoruba - Èdè Yorùbá",
            "zu" => "Zulu - isiZulu",
        );
        return array_key_exists($key, $languages) ? $languages[$key] : $key;
    }

    public static function get_view_keys()
    {
        $keys = BusinessSetting::whereIn('key', ['toggle_veg_non_veg','toggle_dm_registration', 'toggle_restaurant_registration'])->get();
        $data = [];
        foreach($keys as $key)
        {
            $data[$key->key] = (boolean)$key->value;
        }
        return $data;
    }

    public static function generate_dm_time_logs(Shift $shift,$dm_id,Carbon $date, $status="no_show")
    {
        if(count($shift->special_shifts)){
            $shift->start_time = $shift->special_shifts[0]->start_time;
            $shift->end_time = $shift->special_shifts[0]->end_time;
        }
        $dm_suspension = SuspensionLog::where('suspension_start','<=',$date->format('Y-m-d H:i:s'))->where('suspension_end','>=',$date->format('Y-m-d H:i:s'))->where('delivery_man_id',$dm_id)->first();
        $start_time = Carbon::parse($shift->start_time);
        $end_time = Carbon::parse($shift->end_time);
        $time_difference_in_hour = $end_time->diffInHours($start_time);
        return [
            "delivery_man_id" => $dm_id,
            "zone_id" => $shift->zone_id,
            "shift_id" => $shift->id,
            "start_time" => $shift->start_time,
            "end_time" => $shift->end_time,
            "duration" => $status=="performed"?$time_difference_in_hour:0,
            "status" => $dm_suspension?'no_show_excused':$status,
            "date" => $date->format('Y-m-d'),
            'created_at' => now()
        ];
    }

    public static function set_time_log($user_id , $date, $online = null, $offline = null)
    {
        try {
            $time_log = TimeLog::where(['user_id'=>$user_id, 'date'=>$date])->first();
            // dd($online, $offline);
            if($time_log && $time_log->online && $online) return true;

            if($offline && $time_log) {
                $time_log->offline = $offline;
                $time_log->online_time = (strtotime($offline) - strtotime($time_log->online))/60;
                $time_log->save();
                
                return true;
            }

            $time_log = new TimeLog;
            $time_log->date = $date;
            $time_log->user_id = $user_id;
            $time_log->offline = $offline;
            $time_log->online = $online;
            $time_log->save();
            return true;
        } catch(\Exception $ex) {
            info($ex);
        }
        return false;
    }

    public static function dm_time_log($user_id , $date, $order_id, $accepted = null, $delivered = null)
    {
        try {
            $time_log = TimeLog::where(['user_id'=>$user_id, 'date'=>$date])->firstOrNew();
            $order = Order::find($order_id);
            $dm = DeliveryMan::find($user_id);

            $status = array('canceled', 'failed', 'delivered');

            if($dm->current_orders > 1)  return true;

            if($dm->current_orders == 1 && ($time_log->accepted !=null) && !(in_array($order->order_status, $status)))
            {
                if(strtotime($accepted)>strtotime($time_log->delivered)){

                    $time_log->accepted = $accepted;
                    $time_log->idle_time = (strtotime($accepted) - strtotime($time_log->delivered))/60;
                    $time_log->save();
                    return true;

                }
            }
            else if(in_array($order->order_status, $status) && $delivered){

                $time_log->delivered = $delivered;
                $time_log->save();
                return true;
            }

            $time_log->accepted = $accepted;
            $time_log->save();
            return true;

        } catch(\Exception $ex) {
            info($ex);
        }
        return false;
    }

    public static function delivery_time_log($date, $time_in_minutes, $time_1, $time_2, $order_id, $status)
    {

        try {
            $order = Order::with('delivery_man')->find($order_id);


                $datetime1 = Carbon::parse($time_1)->addMinute($time_in_minutes);
                $datetime2 = Carbon::parse($time_2);
                $interval = $datetime2->gte($datetime1);
                $date_for_count = date("Y-m-d",strtotime($date));
                if($status == 'delivered'){
                    
                    if($interval){
                        $order->late_delivery = 1;
                        $order->save();
                        $t = TimeLog::where(['user_id' => $order->delivery_man->id])->whereDate('date', '=', $date_for_count)->first();
                        $t->late_delivery += 1;
                        $t->save();
                        return true;
                    }
                    else{
    
                        $order->on_time_delivery = 1;
                        $order->save();
                        $t = TimeLog::where(['user_id' => $order->delivery_man->id])->whereDate('date', '=', $date_for_count)->first();
                        $t->on_time_delivery += 1;
                        $t->save();
                        return true;
                    }
                }
                else if($status == 'picked_up'){

                    if($interval){
                        $order->late_pickup = 1;
                        $order->save();
                        $t = TimeLog::where(['user_id' => $order->delivery_man->id])->whereDate('date', '=', $date_for_count)->first();
                        $t->late_pickup += 1;
                        $t->save();
                        return true;
                    }
                }


        } catch (\Exception $ex) {
            info($ex);
        }
    }
    public static function shift_time_log($time_log , $online = null, $offline = null)
    {
        
        try {

            if($time_log && $time_log->shift_time_online && $online) return true;
            
            if($time_log->shift_time_online !=null && $offline){
                $shift_offline = $offline->gte($time_log->end_time)?$time_log->end_time:$offline;
                $time_log->shift_time_offline = $shift_offline;
                $start_time = Carbon::parse($time_log->shift_time_online);
                $end_time = Carbon::parse($shift_offline);
                $time_difference_in_hour = $end_time->diffInMinutes($start_time);
                // dd($start_time, $end_time);
                $time_log->total_working_time = $time_difference_in_hour;
                $time_log->save();

                return true;
            }
            $time_log->shift_time_online = $online;
            $time_log->save();
        } catch (\Exception $e) {
            echo $e;
        }
    }

    public static function response_formatter($content = null, $limit =null, $offset = null ,$errors = []): array
    {
        $constant['total_size'] = isset($limit)?$content->total():null;
        $constant['limit'] = $limit;
        $constant['offset'] = $offset;
        $constant['data'] = $content;
        $constant['errors'] = $errors;
        return $constant;
    }

    public static function get_routes(array $originCoordinates, array $destinationCoordinates, array $intermediateCoordinates = [], array $drivingMode = ["DRIVE"])
    {
        $encoded_polyline = null;
        $distance = 0;
        $duration = 0;
        $responses = [];
        $origin = $originCoordinates['lat'] . ',' . $originCoordinates['lng'];
        $destination = $destinationCoordinates['lat'] . ',' . $destinationCoordinates['lng'];
        $waypoints = [];

        if (!empty($intermediateCoordinates)) {
            foreach ($intermediateCoordinates as $coordinates) {
                $waypoints[] = $coordinates['lat'] . ',' . $coordinates['lng'];
            }
        }

        $waypointsParam = '';
        if (!empty($waypoints)) {
            $waypointsParam = implode('|', $waypoints);
        }

        $map_api_key_server=BusinessSetting::where(['key'=>'map_api_key_server'])->first()?->value ?? null;

        $url = 'https://maps.googleapis.com/maps/api/directions/json?destination=' . $destination . '&origin=' . $origin . '&waypoints=' .
            $waypointsParam . '&key=' . $map_api_key_server;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $result = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($result);
        if ($result->status == 'OK') {
            foreach ($result->routes as $route) {
                $encoded_polyline = $route->overview_polyline->points;
                foreach ($route->legs as $leg) {
                    $distance += $leg->distance->value;
                    $duration += $leg->duration->value;
                }
            }

            $responses = [
                'distance' => round($distance, 2),
                'distance_text' => number_format(($distance ?? 0) / 1000, 2) . ' ' . 'km',
                'duration' => number_format(($duration / 60), 2) . ' ' . 'min',
                'duration_sec' => (int)$duration,
                'status' => "OK",
                'drive_mode' => 'DRIVE',
                'encoded_polyline' => $encoded_polyline,
            ];
        } else {
            $responses = [
                'message' => trans('messages.some_error_occurred'),
                'error_detail' => $result?->error_message ?? 'wrong locations provided',
                'status' => $result->status
            ];
        }

        return $responses;
    }

    // public static function get_routes($originCoordinates, $destinationCoordinates, $intermediateCoordinates = [], $drivingMode = ["DRIVE"])
    // {
    //     $responses = [];
    //     $intermediates =[];
    //     if (!empty($intermediateCoordinates)) {
    //         foreach ($intermediateCoordinates as $coordinate) {
    //             $intermediates[] = [
    //                 'location' => [
    //                     'latLng' => [
    //                         'latitude' => $coordinate['lat'],
    //                         'longitude' => $coordinate['lat']
    //                     ]
    //                 ]
    //             ] ;
    //         }
    //     }
    //     foreach ($drivingMode as $key => $value) {
    //         $postData = [
    //             "origin" => [
    //                   "location" => [
    //                      "latLng" => [
    //                         "latitude" => $originCoordinates['lat'],
    //                         "longitude" => $originCoordinates['lng']
    //                      ]
    //                   ]
    //                ],
    //             "destination" => [
    //                            "location" => [
    //                               "latLng" => [
    //                                  "latitude" => $destinationCoordinates['lat'],
    //                                  "longitude" => $destinationCoordinates['lng']
    //                               ]
    //                            ]
    //                         ],
    //             "intermediates" => $intermediates,
    //             "travelMode" => $value,
    //             "extraComputations" => "TRAFFIC_ON_POLYLINE",
    //             "routingPreference" => "TRAFFIC_AWARE_OPTIMAL",
    //             "computeAlternativeRoutes"=> false,
    //             "polylineQuality" => "OVERVIEW",
    //             "routeModifiers" => [
    //               "avoidTolls" => false,
    //               "avoidHighways" => false,
    //               "avoidFerries" => false
    //             ],
    //             "languageCode" => "en-US",
    //             "units" => "METRIC"
    //         ];


    //         $headers = array();
    //         $headers[] = 'Content-Type: application/json';
    //         $headers[] = 'X-Goog-Api-Key: AIzaSyDGFRelTRtPB6QNGOQcT6xE1eMLHHLylY0';
    //         $headers[] = 'X-Goog-Fieldmask: routes.duration,routes.distanceMeters,routes.polyline.encodedPolyline';

    //         $url = 'https://routes.googleapis.com/directions/v2:computeRoutes';
    //         $result = Helpers::send_curl_request($url,json_encode($postData, true),$headers);
    //         $decode_result = json_decode($result, true);
    //         $decode_result['drivingMode'] = $value;

    //         if(!empty($decode_result) && isset($decode_result['routes']) && !isset($decode_result['error'])){
    //                 $duration_sec = (int)rtrim($decode_result['routes'][0]['duration'],'s');
    //                 $duration = floor($duration_sec>0?$duration_sec/60:0);
    //                 $distance_in_meter = $decode_result['routes'][0]['distanceMeters'] ?? null;
    //                 $distance_in_text = number_format(($decode_result['routes'][0]['distanceMeters'] ?? 0)/1000, 1).' '.'km';
    //                 $encoded_polyline = $decode_result['routes'][0]['polyline']['encodedPolyline'];
    //                 $drive_mode = $decode_result['drivingMode'];

    //                 $routes = [
    //                     'distance' => $distance_in_meter,
    //                     'distance_text' => $distance_in_text,
    //                     'duration' => $duration.' '.'min',
    //                     'duration_sec' => $duration_sec,
    //                     'status' => "OK",
    //                     'drive_mode' => $drive_mode,
    //                     'encoded_polyline' =>$encoded_polyline
    //                 ];

    //         $responses[$key] = $routes;

    //         }else{
    //             $responses[$key] = [
    //                     'message' => trans('lang.pick_up_or_destination_points_are_wrong'),
    //                     'message_from_google' => isset($decode_result['error']) == true?$decode_result['error']:null,
    //                     'status' => null,
    //                 ];
    //         }
    //     }
    //     return $responses;
    // }

    public static function send_curl_request(string $url, string $postdata, array $header): string|bool
    {
        $ch = curl_init();
        $timeout = 120;
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);

        // Get URL content
        $result = curl_exec($ch);
        curl_close($ch);

        return $result;
    }

    public static function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'km') {
        $radius = ($unit == 'km') ? 6371 : 3959; // Radius of the Earth in either kilometers or miles
    
        $latDelta = deg2rad($lat2 - $lat1);
        $lonDelta = deg2rad($lon2 - $lon1);
    
        $a = sin($latDelta / 2) * sin($latDelta / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($lonDelta / 2) * sin($lonDelta / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
    
        $distance = $radius * $c;
    
        return $distance;
    }
}
