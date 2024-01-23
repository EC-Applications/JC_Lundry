<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Models\DeliveryMan;
use Brian2694\Toastr\Facades\Toastr;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Grimzy\LaravelMysqlSpatial\Types\Polygon;
use Grimzy\LaravelMysqlSpatial\Types\LineString;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;
use Modules\LaundryManagement\Entities\LaundryOrder;
use Modules\LaundryManagement\Entities\LaundryZone;

class LaundryZoneController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $zones = LaundryZone::withCount(['deliverymen'])->latest()->paginate(config('default_pagination'));
        return view('laundrymanagement::admin-views.zone.index', compact('zones'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:zones|max:191',
            'coordinates' => 'required',
        ]);

        $value = $request->coordinates; 
        foreach(explode('),(',trim($value,'()')) as $index=>$single_array){
            if($index == 0)
            {
                $lastcord = explode(',',$single_array);
            }
            $coords = explode(',',$single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }
        $zone_id=LaundryZone::all()->count() + 1;
        $polygon[] = new Point($lastcord[0], $lastcord[1]);
        $zone = new LaundryZone();
        $zone->name = $request->name;
        $zone->coordinates = new Polygon([new LineString($polygon)]);
        $zone->save();

        Toastr::success(trans('messages.zone_added_successfully'));
        return back();
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        if(env('APP_MODE')=='demo' && $id == 1)
        {
            Toastr::warning(trans('messages.you_can_not_edit_this_zone_please_add_a_new_zone_to_edit'));
            return back();
        }
        $zone=LaundryZone::selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->findOrFail($id);
        // dd($zone->coordinates);
        return view('laundrymanagement::admin-views.zone.edit', compact('zone'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        if(env('APP_MODE')=='demo' && $id == 1)
        {
            Toastr::warning(trans('messages.you_can_not_edit_this_zone_please_add_a_new_zone_to_edit'));
            return back();
        }
        $zone=LaundryZone::selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->findOrFail($id);
        // dd($zone->coordinates);
        return view('laundrymanagement::admin-views.zone.edit', compact('zone'));
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
            'name' => 'required|max:191|unique:zones,name,'.$id,
            'coordinates' => 'required',
        ]);
        $value = $request->coordinates; 
        foreach(explode('),(',trim($value,'()')) as $index=>$single_array){
            if($index == 0)
            {
                $lastcord = explode(',',$single_array);
            }
            $coords = explode(',',$single_array);
            $polygon[] = new Point($coords[0], $coords[1]);
        }
        $polygon[] = new Point($lastcord[0], $lastcord[1]);
        $zone=LaundryZone::findOrFail($id);
        $zone->name = $request->name;
        $zone->coordinates = new Polygon([new LineString($polygon)]);
        $zone->save();
        Toastr::success(trans('messages.zone_updated_successfully'));
        return redirect()->route('admin.laundry.zone.home');
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy(LaundryZone $zone)
    {
        if(env('APP_MODE')=='demo' && $zone->id == 1)
        {
            Toastr::warning(trans('messages.you_can_not_delete_this_zone_please_add_a_new_zone_to_delete'));
            return back();
        }
        $zone->delete();
        Toastr::success(trans('messages.zone_deleted_successfully'));
        return back();
    }

    /**
     * Summary of status
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function status(Request $request)
    {
        if(env('APP_MODE')=='demo' && $request->id == 1)
        {
            Toastr::warning('Sorry!You can not inactive this zone!');
            return back();
        }
        $zone = LaundryZone::findOrFail($request->id);
        $zone->status = $request->status;
        $zone->save();
        Toastr::success(trans('messages.zone_status_updated'));
        return back();
    }

    /**
     * Summary of search
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function search(Request $request){
        $key = explode(' ', $request['search']);
        $zones=LaundryZone::where(function ($q) use ($key) {
            foreach ($key as $value) {
                $q->orWhere('name', 'like', "%{$value}%");
            }
        })->limit(50)->get();
        return response()->json([
            'view'=>view('admin-views.zone.partials._table',compact('zones'))->render(),
            'total'=>$zones->count()
        ]);
    }

    /**
     * Summary of get_coordinates
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_coordinates($id){
        $zone=LaundryZone::withoutGlobalScopes()->selectRaw("*,ST_AsText(ST_Centroid(`coordinates`)) as center")->findOrFail($id);
        $data = Helpers::format_coordiantes($zone->coordinates[0]);
        $center = (object)['lat'=>(float)trim(explode(' ',$zone->center)[1], 'POINT()'), 'lng'=>(float)trim(explode(' ',$zone->center)[0], 'POINT()')];
        return response()->json(['coordinates'=>$data, 'center'=>$center]);
    }

    /**
     * Summary of zone_filter
     * @param mixed $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function zone_filter($id)
    {
        if($id == 'all')
        {
            if(session()->has('zone_id')){
                session()->forget('zone_id');
            }
        }
        else{
            session()->put('zone_id', $id);
        }
        
        return back();
    }

    /**
     * Summary of get_all_zone_cordinates
     * @param mixed $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get_all_zone_cordinates($id = 0)
    {
        $zones = LaundryZone::where('id', '<>', $id)->active()->get();
        $data = [];
        foreach($zones as $zone)
        {
            $data[] = Helpers::format_coordiantes($zone->coordinates[0]);
        }
        return response()->json($data,200);
    }


}
