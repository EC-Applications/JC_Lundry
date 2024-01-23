<?php

namespace Modules\LaundryManagement\Http\Controllers\Api;

use App\CentralLogics\Helpers;
use Grimzy\LaravelMysqlSpatial\Types\Point;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class LaundryConfigController extends Controller
{

    /**
     * Summary of get_routes
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse|array<array>
     */
    public function get_routes(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pickup_coordinates' => 'required',
            'destination_coordinates' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)]);
        }

        $pickup_coordinates = ['lat' => $request->pickup_coordinates[0], 'lng' => $request->pickup_coordinates[1]];
        $destination_coordinates = ['lat' => $request->destination_coordinates[0], 'lng' => $request->destination_coordinates[1]];
        $intermediateCoordinates = isset($request->intermediate_coordinates) ? ['lat' => $request->intermediate_coordinates[0], 'lng' => $request->intermediate_coordinates[1]] : [] ;

        return Helpers::get_routes(
            originCoordinates:$pickup_coordinates, 
            destinationCoordinates:$destination_coordinates,
            intermediateCoordinates:$intermediateCoordinates, 
            drivingMode:['DRIVE']
        ); //["DRIVE", "TWO_WHEELER"]
    }
}
