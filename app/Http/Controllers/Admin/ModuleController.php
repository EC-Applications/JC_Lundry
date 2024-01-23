<?php

namespace App\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Http\Controllers\Controller;
use App\Models\Module;
use App\Models\Translation;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ModuleController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function index()
    {
        $modules = Module::withCount('restaurants')->latest()->paginate(config('default_pagination'));
        return view('admin-views.module.index',compact('modules'));
    }

    /**
     * Show the form for creating a new resource.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function create()
    {
        return view('admin-views.module.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'module_name' => 'required|unique:modules|max:100',
        ], [
            'module_name.required' => trans('messages.Name is required!'),
        ]);

        $module = new Module();
        $module->module_name = $request->module_name;
        $module->thumbnail = Helpers::upload('module/', 'png', $request->file('image'));
        $module->module_type= $request->module_type;
        $module->save();

        Toastr::success(trans('messages.module_updated_successfully'));
        return back();
    }

    /**
     * Display the specified resource.
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $module = Module::findOrFail($id);
        return response()->json(['data'=>config('module.'.$module->module_type)]);
    }

    /**
     * Show the form for editing the specified resource.
     * @param mixed $id
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View
     */
    public function edit($id)
    {
        $module = Module::findOrFail($id);
        return view('admin-views.module.edit', compact('module'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'module_name' => 'required|max:100|unique:modules,module_name,'.$id,
        ], [
            'module_name.required' => trans('messages.Name is required!'),
        ]);
        $module = Module::findOrFail($id);

        $module->module_name = $request->module_name;
        $module->thumbnail = $request->has('image') ? Helpers::update('module/', $module->thumbnail, 'png', $request->file('image')) : $module->thumbnail;
        // $module->module_type = $request->module_type;
        $module->save();

        Toastr::success(trans('messages.category_updated_successfully'));
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $module = Module::findOrFail($id);
        if($module->thumbnail)
        {
            if (Storage::disk('public')->exists('module/' . $module['thumbnail'])) {
                Storage::disk('public')->delete('module/' . $module['thumbnail']);
            }
        }
        $module->translations()->delete();
        $module->delete();
        Toastr::success(trans('messages.module_deleted_successfully'));
        return back();
    }

    /**
     * Summary of status
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function status(Request $request)
    {
        $module = Module::find($request->id);
        $module->status = $request->status;
        $module->save();
        Toastr::success(trans('messages.module_status_updated'));
        return back();
    }

    /**
     * Summary of type
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function type(Request $request)
    {
        return response()->json(['data'=>config('module.'.$request->module_type)]);
    }
}
