<?php

namespace App\CentralLogics;

use App\Models\Category;
use App\Models\Food;
use App\Models\Restaurant;

class CategoryLogic
{
    public static function parents()
    {
        return Category::where('position', 0)->get();
    }

    public static function child($parent_id)
    {
        return Category::where(['parent_id' => $parent_id])->get();
    }

    public static function products(int $category_id, int $zone_id, int $limit,int $offset, $type)
    {
        $paginator = Food::whereHas('restaurant', function($query)use($zone_id){
            return $query->where('zone_id', $zone_id);
        })
        ->whereHas('category',function($q)use($category_id){
            return $q->whereId($category_id)->orWhere('parent_id', $category_id);
        })
        ->when(config('module.current_module_id'), function($query){
            $query->module(config('module.current_module_id'));
        })
        ->active()->type($type)->latest()->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'products' => $paginator->items()
        ];
    }


    public static function restaurants(int $category_id, int $zone_id, int $limit,int $offset, $type)
    {
        $paginator = Restaurant::where('zone_id', $zone_id)
        ->whereHas('foods.category', function($query)use($category_id){
            return $query->whereId($category_id)->orWhere('parent_id', $category_id);
        })
        // ->whereHas('category',function($q)use($category_id){
        //     return $q->whereId($category_id)->orWhere('parent_id', $category_id);
        // })
        ->when(config('module.current_module_id'), function($query){
            $query->module(config('module.current_module_id'));
        })
        ->active()->type($type)->latest()->paginate($limit, ['*'], 'page', $offset);

        return [
            'total_size' => $paginator->total(),
            'limit' => $limit,
            'offset' => $offset,
            'restaurants' => $paginator->items()
        ];
    }


    public static function all_products($id, $zone_id)
    {
        $cate_ids=[];
        array_push($cate_ids,(int)$id);
        foreach (CategoryLogic::child($id) as $ch1){
            array_push($cate_ids,$ch1['id']);
            foreach (CategoryLogic::child($ch1['id']) as $ch2){
                array_push($cate_ids,$ch2['id']);
            }
        }

        // $products = Food::active()->get();
        // $product_ids = [];
        // foreach ($products as $product) {
        //     foreach (json_decode($product['category_ids'], true) as $category) {
        //         if (in_array($category['id'],$cate_ids)) {
        //             array_push($product_ids, $product['id']);
        //         }
        //     }
        // }

        return Food::whereIn('category_id', $cate_ids)
        ->when(config('module.current_module_id'), function($query){
            $query->module(config('module.current_module_id'));
        })
        ->get();
    }
}
