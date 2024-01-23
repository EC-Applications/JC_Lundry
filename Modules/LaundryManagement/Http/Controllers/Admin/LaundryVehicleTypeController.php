<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\LaundryManagement\Entities\LaundryVehicleType;

class LaundryVehicleTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $types = LaundryVehicleType::latest()->paginate(config('default_pagination'));
        return view('laundrymanagement::admin-views.vehicle-type.index', compact('types'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:191',
        ]);

        $banner = new LaundryVehicleType;
        $banner->name = $request->name;
        $banner->save();

        Toastr::success(trans('messages.vehicle_type_added_successfully'));
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $type = LaundryVehicleType::findOrFail($id);
        return view('laundrymanagement::admin-views.vehicle-type.edit', compact('type'));
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
            'name' => 'required|max:191',
        ]);

        $type = LaundryVehicleType::findOrFail($id);
        $type->name = $request->name;
        $type->save();

        Toastr::success(trans('messages.vehicle_type_updated_successfully'));
        return redirect()->route('admin.laundry.vehicle-type.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $banner = LaundryVehicleType::findOrFail($id);
        $banner->delete();
        Toastr::success(trans('messages.vehicle_type_deleted_successfully'));
        return back();
    }

    /**
     * Summary of search
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $types= LaundryVehicleType::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->where('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('laundrymanagement::admin-views.vehicle-type.partials._table',compact('types'))->render(),
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
        $banner = LaundryVehicleType::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();

        Toastr::success(trans('messages.vehicle_type_status_updated_successfully'));
        return back();
    }
}
