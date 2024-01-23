<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\DMTimeLog;
use App\Models\DeliveryMan;
use App\Models\Shift;
use App\Models\SuspensionLog;
use Carbon\CarbonPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Validator;

class ShiftController extends Controller
{
    public function get_shifts(Request $request){
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();

        $zone_id= $dm->zone_id;

        $shifts = Shift::with(['special_shifts'=>function($query){
            $query->where('special_date', now()->format('Y-m-d'));
        },'shift'])->where('end_time','>',now()->format('H:i:s'))->where('zone_id', $zone_id)->get();

        $shifts = array_map(function($shift){
            $shift['start_time'] = date('H:i:s',strtotime($shift['start_time']));
            $shift['end_time'] = date('H:i:s', strtotime($shift['end_time']));
            if(!isset($shift['title']) && isset($shift['shift'])) $shift['title'] = $shift['shift']['title'];
            return $shift;
        },$shifts->toArray());
        return response()->json($shifts,200);
    }


    public function store_time_log(Request $request){
        $validator = Validator::make($request->all(), [
            'shift_id' => 'required'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        $shift_logs = [];
        $dm = DeliveryMan::where(['auth_token' => $request['token']])->first();
        $today = now();
        if(!$shift = Shift::with(['special_shifts'=>function($query)use($today){
            $query->where('special_date', $today->format('Y-m-d'));
        }])->where('end_time', '>' , now()->format('H:i:s'))->whereId($request->shift_id)->first()){
            return response()->json([
                'errors' => [['code' => 'shift_id', 'message' => trans('messages.shift_not_found')]]
            ], 403);
        }

        $shifts = Shift::where('zone_id',$shift->zone_id);
        $last_log_shift = $dm->time_logs()->latest()->first();

        $present_no_show_shifts = $shifts->with('special_shifts',function($query)use($today){
            $query->where('special_date', $today->format('Y-m-d'));
        })->where('start_time','<',$shift->end_time)->orderBy('start_time')->whereNull('parent_id')->get();
        // dd($present_no_show_shifts);
        if($last_log_shift){
            $last_log_shift_date = Carbon::parse($last_log_shift->date);

            if($last_log_shift->shift_id == $shift->id && $last_log_shift->date == now()->format('Y-m-d')){
                return response()->json(['message' => trans('messages.log_create_success_message')], 200);
            }


            $last_day_no_show_shifts = $shifts->with('special_shifts',function($query)use($last_log_shift_date){
                    $query->where('special_date', $last_log_shift_date->format('Y-m-d'));
                })->whereTime('end_time','>',$last_log_shift->end_time)->orderBy('start_time')->whereNull('parent_id')->get();

            foreach($last_day_no_show_shifts as $data){
                if(!(($data->id == $request->shift_id && $last_log_shift->delivery_man_id==$dm->id))){

                    $shift_logs[]=Helpers::generate_dm_time_logs($data,$dm->id,Carbon::parse($last_log_shift->date));

                }
            }
                $try = [];

            for($i=Carbon::parse($last_log_shift->date)->addDay();$i->lt(now()->format(date('Y-m-d')));$i->addDay()){

                foreach($shifts->with('special_shifts',function($query)use($i){
                    $query->where('special_date', $i->format('Y-m-d'));
                })->get() as $data){
                    
                    $shift_logs[]=Helpers::generate_dm_time_logs($data,$dm->id,$i);

                }
                
            }
            foreach($present_no_show_shifts as $data){
                
                if(!($data->id == $request->shift_id && $last_log_shift->delivery_man_id==$dm->id))
                {

                    $shift_logs[]=Helpers::generate_dm_time_logs($data,$dm->id,Carbon::parse(now()));
                }
                
            }
        }

        $shift_logs[]=Helpers::generate_dm_time_logs($shift,$dm->id,now(), 'performed');
        // dd($shift_logs);
        DMTimeLog::insert($shift_logs);
       
        return response()->json(['message' => trans('messages.log_create_success_message')], 200);
    }
}
