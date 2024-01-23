<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\DMTimeLog;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use App\Models\Shift;
use App\Models\TimeLog;
use Illuminate\Support\Facades\DB;

class ShiftController extends Controller
{
    function index(Request $request)
    {
        $shifts = Shift::when($request->query('zone_id'), function($query)use($request){
            $query->where('zone_id', $request->query('zone_id'));
        })->whereNull('parent_id')->paginate(config('default_pagination'));
        return view('admin-views.shift.list',compact('shifts'));
    }

    function shift_riders(Request $request)
    {
        // dd($request->query('zone_id'));
        // $request->zone_id = 1;
        $zone_id = $request->zone_id?$request->zone_id:0;

        $shifts = Shift::when($zone_id, function($query)use($zone_id){
            $query->where('zone_id', $zone_id)->withCount(['time_log as total_dm' => function($q)use($zone_id){
                $q->where('zone_id', $zone_id)->select(DB::raw('count(distinct(delivery_man_id))'));
            }]);
        })->whereNull('parent_id')->paginate(config('default_pagination'));



        // dd($shifts);
        return view('admin-views.shift.shift-riders',compact('shifts'));
    }

    function create()
    {
        return view('admin-views.shift.index');
    }
    function create_special()
    {
        return view('admin-views.shift.special');
    }

    function store(Request $request)
    {

        $request->validate([
            'start_time'=>'required|date_format:H:i',
            'end_time'=>'required|date_format:H:i|after:start_time',
            'zone_id'=>'required'
        ],[
            'end_time.after'=>__('messages.End time must be after the start time')
        ]);

        // if(!$request->parent_id){
        //     $temp = Shift::where('zone_id',$request->zone_id)
        //     ->where(function($q)use($request){
        //         return $q->where(function($query)use($request){
        //             return $query->where('start_time', '<=' , $request->start_time)->where('end_time', '>=', $request->start_time);
        //         })->orWhere(function($query)use($request){
        //             return $query->where('start_time', '<=' , $request->end_time)->where('end_time', '>=', $request->end_time);
        //         });
        //     })->whereNull('parent_id')
        //     ->first();
        //     if(isset($temp))
        //     {
        //         Toastr::error(trans('messages.schedule_overlapping_warning'));
        //         return back();
        //     }
        // }


        $shift = new Shift();
        $shift->title = $request->title;
        $shift->zone_id = $request->zone_id;
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        if($request->parent_id){
            $data = Shift::where(['zone_id'=>$request->zone_id,'parent_id'=>$request->parent_id,'special_date'=>$request->date])->first();
            if($data){
                Toastr::error(trans('messages.special_shift_already_exists'));
                return back();
            }
            $shift->special_date = $request->date;
            $shift->parent_id = $request->parent_id;
            $shift->is_special = 1;
        }
        $shift->save();
        Toastr::success(trans('messages.shift_saved_successfully'));
        if($request->parent_id) return back();
        return redirect()->route('admin.shift.list');
    }

    public function edit($id)
    {
        $shift = Shift::find($id);
        return view('admin-views.shift.edit', compact('shift'));
    }

    public function view($id)
    {
        $shift = Shift::find($id);
        return view('admin-views.shift.view', compact('shift'));
    }

    public function get_shifts(Request $request)
    {
        $cat = Shift::where(['zone_id' => $request->zone_id])->get();
        $res = '<option value="' . 0 . '" disabled selected>---Select---</option>';
        foreach ($cat as $row) {
            if($row->parent_id==null){

                $res .= '<option value="' . $row->id . '">' . $row->title . '</option>';
            }
        }
        return response()->json([
            'options' => $res,
        ]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'start_time'=>'required|date_format:H:i',
            'end_time'=>'required|date_format:H:i|after:start_time',
            'zone_id'=>'required',
            'title'=>'required',
        ],[
            'end_time.after'=>__('messages.End time must be after the start time')
        ]);

        // $temp = Shift::where('zone_id',$request->zone_id)->where('id', '!=', $id)
        // ->where(function($q)use($request){
        //     return $q->where(function($query)use($request){
        //         return $query->where('start_time', '<=' , $request->start_time)->where('end_time', '>=', $request->start_time);
        //     })->orWhere(function($query)use($request){
        //         return $query->where('start_time', '<=' , $request->end_time)->where('end_time', '>=', $request->end_time);
        //     });
        // })
        // ->first();

        // if(isset($temp))
        // {
        //     Toastr::error(trans('messages.schedule_overlapping_warning'));
        //     return back();
        // }

        $shift = Shift::find($id);
        $shift->title = $request->title;
        $shift->zone_id = $request->zone_id;
        $shift->start_time = $request->start_time;
        $shift->end_time = $request->end_time;
        $shift->save();
        Toastr::success(trans('messages.shift_updated_successfully'));
        return back();
    }

    public function delete(Request $request)
    {
        $shift = Shift::findOrFail($request->id);
        if ($shift->special_shifts){
            $shift->special_shifts()->delete();
        }
        $shift->delete();
        Toastr::success('Shift removed!');
        return back();
    }
}
