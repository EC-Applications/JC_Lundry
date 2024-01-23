<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\LaundryManagement\Entities\Services;

class ServicesController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $service_list = Services::orderBy('name')->paginate(config('default_pagination'));
        return view('laundrymanagement::admin-views.services.index', compact('service_list'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|unique:services,name',
            'icon'=>'required|image|max:5000',
        ]);

        $service = new Services;
        $service->name = $request->name;
        $service->icon = Helpers::upload('services/', 'png', $request->file('icon'));
        $service->save();

        Toastr::success(trans('messages.service_added_successfully'));
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $service= Services::findOrFail($id);
        return view('laundrymanagement::admin-views.services.edit',compact('service'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return RedirectResponse
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required|max:191|unique:laundry_categories,name,'.$id,
        ]);

        $service = Services::findOrFail($id);
        $service->name = $request->name;
        $service->icon = Helpers::update('services/', $service->icon, 'png', $request->file('icon'));
        $service->save();

        Toastr::success(trans('messages.service_updated_successfully'));
        return redirect()->route('admin.laundry.service.index');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $service = Services::findOrFail($id);
        if($service->icon)
        {
            if (Storage::disk('public')->exists('services/' . $service['icon'])) {
                Storage::disk('public')->delete('services/' . $service['icon']);
            }
        }
        $service->delete();
        Toastr::success(trans('messages.service_deleted_successfully'));
        return back();
    }

     /**
     * Summary of status
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request)
    {
        $service = Services::findOrFail($request->id);
        $service->status = $request->status;
        $service->save();
        Toastr::success(trans('messages.service_status_updated'));
        return back();
    }
}
