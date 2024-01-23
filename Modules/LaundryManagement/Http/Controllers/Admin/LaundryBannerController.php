<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Modules\LaundryManagement\Entities\LaundryBanner;

class LaundryBannerController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $banners = LaundryBanner::with('service')->latest()->paginate(config('default_pagination'));
        return view('laundrymanagement::admin-views.banner.index', compact('banners'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|max:191',
            'image' => 'required',
            'services_id' => 'required',
        ]);

        $banner = new LaundryBanner;
        $banner->title = $request->title;
        $banner->services_id = $request->services_id;
        $banner->image = Helpers::upload('laundry-banners/', 'png', $request->file('image'));
        $banner->save();

        Toastr::success(trans('messages.banner_added_successfully'));
        return back();
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $banner = LaundryBanner::findOrFail($id);
        return view('laundrymanagement::admin-views.banner.edit', compact('banner'));
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
            'title' => 'required|max:191',
        ]);

        $banner = LaundryBanner::findOrFail($id);
        $banner->title = $request->title;
        $banner->services_id = $request->services_id;
        $banner->image = Helpers::update('laundry-banners/', $banner->image, 'png', $request->file('image'));
        $banner->save();

        Toastr::success(trans('messages.banner_updated_successfully'));
        return redirect()->route('admin.laundry.banner.index');

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return RedirectResponse
     */
    public function destroy($id)
    {
        $banner = LaundryBanner::findOrFail($id);
        if (Storage::disk('public')->exists('laundry-banners/' . $banner['image'])) {
            Storage::disk('public')->delete('laundry-banners/' . $banner['image']);
        }
        $banner->delete();
        Toastr::success(trans('messages.banner_deleted_successfully'));
        return back();
    }

    /**
     * Summary of search
     * @param Request $request
     * @return JsonResponse
     */
    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $banners= LaundryBanner::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->where('title', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('laundrymanagement::admin-views.banner.partials._table',compact('banners'))->render(),
            'count'=>$banners->count()
        ]);
    }

    /**
     * Summary of status
     * @param Request $request
     * @return RedirectResponse
     */
    public function status(Request $request)
    {
        $banner = LaundryBanner::findOrFail($request->id);
        $banner->status = $request->status;
        $banner->save();

        Toastr::success(trans('messages.banner_status_updated'));
        return back();
    }
}
