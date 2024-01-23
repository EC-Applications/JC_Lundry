<?php

namespace App\Http\Controllers\Admin;

use Exception;
use App\Models\Deal;
use App\Models\Food;
use App\Models\AddOn;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Scopes\RestaurantScope;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class DealController extends Controller
{
    public function index()
    {
        $deals = Deal::withoutGlobalScope(RestaurantScope::class)->latest()->paginate(config('default_pagination'));
        return view('admin-views.deal.list', compact('deals'));
    }

    public function create()
    {
        return view('admin-views.deal.create');
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'image' => 'required',
            'price' => 'required|numeric|between:.01,999999999999.99',
            'restaurant_id' => 'required',
            'description' => 'max:1000',
            'choice'=>'required|array',
            'start_date'=> 'required|date',
            'end_date'=> 'required|date|after:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        try{
            $deal = new Deal();
            $deal->title = $request->title;
            $deal->price = $request->price;
            $deal->start_at = $request->start_date;
            $deal->end_at = $request->end_date;
            $deal->options = array_values($request->choice);
            $deal->image = Helpers::upload('deal/', 'png', $request->file('image'));
            $deal->short_description = $request->description;
            $deal->restaurant_id = $request->restaurant_id;
            $deal->save();            
        }catch(Exception $ex){
            return response()->json(['errors' => [
                ['code'=>'db-error', 'message'=>$ex->getMessage()]
            ]]);
        }

        return response()->json([],200);
    }

    public function edit($deal)
    {
        $deal = Deal::withoutGlobalScope(RestaurantScope::class)->findOrFail($deal);
        $addons = AddOn::withoutGlobalScope(RestaurantScope::class)->where('restaurant_id', $deal->restaurant_id)->selectRaw('id, CONCAT(name, " (",price," '.Helpers::currency_code().')") as text')->get();
        $foods = Food::withoutGlobalScope(RestaurantScope::class)->where('restaurant_id', $deal->restaurant_id)->selectRaw('id, name as text')->get();
        return view('admin-views.deal.edit', compact('deal', 'addons', 'foods'));
    }

    public function show($deal)
    {
        $deal = Deal::withoutGlobalScope(RestaurantScope::class)->findOrFail($deal);
        // dd($deal);
    }

    public function update(Request $request, $deal)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|max:191',
            'price' => 'required|numeric|between:.01,999999999999.99',
            'restaurant_id' => 'required',
            'description' => 'max:1000',
            'choice'=>'required|array',
            'start_date'=> 'required|date',
            'end_date'=> 'required|date|after:start_date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }
        try{
            $deal = Deal::withoutGlobalScope(RestaurantScope::class)->findOrFail($deal);
            $deal->title = $request->title;
            $deal->price = $request->price;
            $deal->start_at = $request->start_date;
            $deal->end_at = $request->end_date;
            $deal->options = array_values($request->choice);
            $deal->image = $request->has('image') ? Helpers::update('deal/', $deal->image, 'png', $request->file('image')) : $deal->image;
            $deal->short_description = $request->description;
            $deal->restaurant_id = $request->restaurant_id;
            $deal->save();            
        }catch(Exception $ex){
            return response()->json(['errors' => [
                ['code'=>'db-error', 'message'=>$ex->getMessage()]
            ]]);
        }

        return response()->json([],200);
    }

    public function update_status($id, $status){
        $deal = Deal::findOrFail($id);
        $deal->status = $status;
        $deal->save();
        Toastr::success(trans('messages.deal_status_updated'));
        return back();
    }

    public function search(Request $request) {

    }

    public function destroy($deal)
    {
        $deal = Deal::withoutGlobalScope(RestaurantScope::class)->findOrFail($deal);
        if($deal->image)
        {
            if (Storage::disk('public')->exists('deal/' . $deal->image)) {
                Storage::disk('public')->delete('deal/' . $deal->image);
            }
        }

        $deal->delete();
        Toastr::success(trans('messages.deal_deleted_successfully'));
        return back();
    }
}
