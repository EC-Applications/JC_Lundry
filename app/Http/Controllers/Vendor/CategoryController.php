<?php

namespace App\Http\Controllers\Vendor;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\CategoryRestaurant;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CategoryController extends Controller
{
    function index()
    {
        $categories=Category::with(['category_restaurant'=>function($query){
            $query->whereRestaurantId(Helpers::get_restaurant_id());
        }])->where(['position'=>0])->latest()->paginate(config('default_pagination'));
        return view('vendor-views.category.index',compact('categories'));
    }

    public function get_all(Request $request){
        $data = Category::where('name', 'like', '%'.$request->q.'%')->limit(8)->get([DB::raw('id, CONCAT(name, " (", if(position = 0, "'.trans('messages.main').'", "'.trans('messages.sub').'"),")") as text')]);
        if(isset($request->all))
        {
            $data[]=(object)['id'=>'all', 'text'=>'All'];
        }
        return response()->json($data);
    }

    function sub_index()
    {
        $categories=Category::with(['category_restaurant' =>function($query){
            $query->whereRestaurantId(Helpers::get_restaurant_id());
        },'parent'])->where(['position'=>1])->latest()->paginate(config('default_pagination'));
        return view('vendor-views.category.sub-index',compact('categories'));
    }

    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $categories=Category::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('vendor-views.category.partials._table',compact('categories'))->render(),
            'count'=>$categories->count()
        ]);
    }

    public function update_priority($id, Request $request)
    {
        $request->validate([
            'priority' => 'required|max:99999999|min:0',
        ]);

            $category = new CategoryRestaurant();
            $priority = $request->priority??0;
            $category->category_id=$id;
            $category->restaurant_id = Helpers::get_restaurant_id();
            $category->priority = $priority;
            $category->save();
            Toastr::success(trans('messages.category_priority_updated successfully'));
            return back();

    }
}
