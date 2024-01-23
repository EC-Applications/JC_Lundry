<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LaundryManagement\Entities\LaundryItem;

class LaundryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $item_list = LaundryItem::latest()->paginate(config('default_pagination'));
        return view('laundrymanagement::admin-views.item.index', compact('item_list'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:laundry_items,name',
            'icon' => 'required|image|max:500'
        ]);

        $item = new LaundryItem;
        $item->name = $request->name;
        $item->icon = Helpers::upload('laundry-items/', 'png', $request->file('icon'));
        $item->save();

        Toastr::success(trans('messages.laundry_item_added_successfully'));
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $item = LaundryItem::with('services')->findOrFail($id);
        return view('laundrymanagement::admin-views.item.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $item = LaundryItem::findOrFail($id);
        $item->name = $request->name;
        $item->icon = Helpers::update('laundry-items/', $item->icon, 'png', $request->file('icon'));
        $item->services()->sync($request->service_data);
        $item->save();

        Toastr::success(trans('messages.serivice_wise_item_price_updated_successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $item = LaundryItem::findOrFail($id);
        $item->services()->detach();
        $item->delete();

        Toastr::success(trans('messages.laundry_item_deleted_successfully'));
        return back();
    }

        /**
     * Summary of status
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request)
    {
        $service = LaundryItem::findOrFail($request->id);
        $service->status = $request->status;
        $service->save();
        Toastr::success(trans('messages.laundry_item_status_updated'));
        return back();
    }

    /**
     * Summary of search
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request){

        $key = explode(' ', $request['search']);
        $item_list=LaundryItem::
        where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();

        return response()->json([
            'view'=>view('laundrymanagement::admin-views.item.partials._table',compact('item_list'))->render(),
            'count'=>$item_list->count()
        ]);
    }

}
