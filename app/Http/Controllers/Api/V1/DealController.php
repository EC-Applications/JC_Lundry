<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use App\Models\Deal;
use Illuminate\Support\Facades\Validator;

class DealController extends Controller
{
    public function get_deals(Request $request){
        $validator = Validator::make($request->all(), [
            'limit' => 'required',
            'offset' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }
        if (!$request->hasHeader('zoneId')) {
            $errors = [];
            array_push($errors, ['code' => 'zoneId', 'message' => trans('messages.zone_id_required')]);
            return response()->json([
                'errors' => $errors
            ], 403);
        }

        $zone_id= $request->header('zoneId');

        $deals = Deal::whereHas('restaurant', function($q)use($zone_id){
            $q->where('zone_id', $zone_id)->Weekday();
        })
        ->when($request->restaurant_id, function($query)use($request){
            $query->where('restaurant_id', $request->restaurant_id);
        })
        ->available(now()->format('Y-m-d H:i:s'))->active()->paginate($request->limit, ['*'], 'page', $request->offset);;
        return response()->json([
            'total_size' => $deals->total(),
            'limit' => $request->limit,
            'offset' => $request->offset,
            'data' => Helpers::format_deal_data($deals->items(), true)
        ]);
    }
}
