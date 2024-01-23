<?php

namespace App\Http\Controllers\Api\V1;

use App\CentralLogics\Helpers;
use App\CentralLogics\RestaurantLogic;
use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Review;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{
    public function get_restaurants(Request $request, $filter_data="all")
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => trans('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }

        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');
        $restaurants = RestaurantLogic::get_restaurants($zone_id, $filter_data, $type, $request['limit'], $request['offset']);
        $restaurants['restaurants'] = Helpers::restaurant_data_formatting($restaurants['restaurants'], true);

        return response()->json($restaurants, 200);
    }

    public function get_latest_restaurants(Request $request, $filter_data="all")
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => trans('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }

        $type = $request->query('type', 'all');
        $discount = $request->query('discount', 'all');

        $zone_id= $request->header('zoneId');
        $restaurants = RestaurantLogic::get_latest_restaurants($zone_id, $type, $discount, $request['limit'], $request['offset']);
        $restaurants['restaurants'] = Helpers::restaurant_data_formatting($restaurants['restaurants'], true);

        return response()->json($restaurants['restaurants'], 200);
    }

    public function get_popular_restaurants(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => trans('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');
        $restaurants = RestaurantLogic::get_popular_restaurants($zone_id, $type, $request['limit'], $request['offset']);
        $restaurants['restaurants'] = Helpers::restaurant_data_formatting($restaurants['restaurants'], true);

        return response()->json($restaurants['restaurants'], 200);
    }

    public function get_discounted_restaurant(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => trans('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }

        $type = $request->query('type', 'all');
        $zone_id= $request->header('zoneId');

        $limit = $request->query('limit', 10);
        $offset = $request->query('offset', 10);
        $data = RestaurantLogic::get_discounted_restaurants($zone_id, $type, $limit, $offset);

        $data['restaurants'] = Helpers::restaurant_data_formatting($data['restaurants'], true);

        return response()->json($data, 200);
    }

    public function get_details($id)
    {
        $restaurant = RestaurantLogic::get_restaurant_details($id);
        if($restaurant)
        {
            $category_ids = DB::table('categories')
            ->rightJoin('food','categories.id', '=','food.category_id')
            ->leftJoin('category_restaurants','category_restaurants.category_id', '=','categories.id')
            ->selectRaw('DISTINCT `categories`.`id` as id, `categories`.`name` as name, IF(`category_restaurants`.`priority` IS NULL, 0, `category_restaurants`.`priority`) as priority')
            ->where('food.restaurant_id', $id)
            ->where('categories.status',1)
            ->where('categories.position', '1')
            ->orderBy('priority', 'desc')
            ->get();

            $restaurant = Helpers::restaurant_data_formatting($restaurant);
            $restaurant['category_ids'] = $category_ids;
        }
        return response()->json($restaurant, 200);
    }

    public function get_searched_restaurants(Request $request)
    {
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => trans('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $type = $request->query('type', 'all');

        $zone_id= $request->header('zoneId');
        $restaurants = RestaurantLogic::search_restaurants($request['name'], $zone_id, $type, $request->category_id,$request['limit'], $request['offset']);
        $restaurants['restaurants'] = Helpers::restaurant_data_formatting($restaurants['restaurants'], true);
        return response()->json($restaurants, 200);
    }

    public function reviews(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'restaurant_id' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $id = $request['restaurant_id'];


        $reviews = Review::with(['customer', 'food'])
        ->whereHas('food', function($query)use($id){
            return $query->where('restaurant_id', $id);
        })
        ->active()->get();

        $storage = [];
        foreach ($reviews as $item) {
            $item['attachment'] = json_decode($item['attachment']);
            $item['food_name'] = $item->food->name;
            unset($item['food']);
            array_push($storage, $item);
        }

        return response()->json($storage, 200);
    }

    // public function get_product_rating($id)
    // {
    //     try {
    //         $product = Food::find($id);
    //         $overallRating = ProductLogic::get_overall_rating($product->reviews);
    //         return response()->json(floatval($overallRating[0]), 200);
    //     } catch (\Exception $e) {
    //         return response()->json(['errors' => $e], 403);
    //     }
    // }

}
