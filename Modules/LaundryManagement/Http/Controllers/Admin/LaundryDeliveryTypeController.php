<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LaundryManagement\Entities\LaundryDeliveryType;

class LaundryDeliveryTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $types = LaundryDeliveryType::latest()->paginate(config('default_pagination'));
        return view('laundrymanagement::admin-views.delivery-types.index', compact('types'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('laundrymanagement::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:laundry_delivery_types,title',
            'duration' => 'required|integer',
            'charge' => 'required',
        ]);

        $type = new LaundryDeliveryType;
        $type->title = $request->title;
        $type->duration = $request->duration;
        $type->charge = $request->charge;
        $type->save();

        Toastr::success(trans('messages.delivery_type_added_successfully'));
        return back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('laundrymanagement::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $type = LaundryDeliveryType::findOrFail($id);
        return view('laundrymanagement::admin-views.delivery-types.edit', compact('type'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|unique:laundry_delivery_types,title,'.$id,
            'duration' => 'required|integer',
            'charge' => 'required',
        ]);

        $type = LaundryDeliveryType::findOrFail($id);
        $type->title = $request->title;
        $type->duration = $request->duration;
        $type->charge = $request->charge;
        $type->save();

        Toastr::success(trans('messages.delivery_type_updated_successfully'));
        return redirect()->route('admin.laundry.delivery-type.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $item = LaundryDeliveryType::findOrFail($id);
        $item->delete();

        Toastr::success(trans('messages.delivery_type_deleted_successfully'));
        return back();
    }

        /**
     * Summary of search
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $types= LaundryDeliveryType::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('title', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('laundrymanagement::admin-views.delivery-types.partials._table',compact('types'))->render(),
            'count'=>$types->count()
        ]);
    }

    /**
     * Summary of status
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request)
    {
        $d_type_all = LaundryDeliveryType::all();
        if ((($d_type_all->count()-1) == $d_type_all->where('status', 0)->count()) && $request->status == 0) {
            Toastr::error(trans('messages.minimum_one_delivery_type_need_to_active'));
            return back();
        }
        
        $delivery_type = LaundryDeliveryType::findOrFail($request->id);
        $delivery_type->status = $request->status;
        $delivery_type->save();

        Toastr::success(trans('messages.delivery_type_status_updated'));
        return back();
    }
}
