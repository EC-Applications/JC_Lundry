<?php

namespace Modules\LaundryManagement\Http\Controllers\Admin;

use App\CentralLogics\Helpers;
use App\Models\DeliveryMan;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Validator;

class LaundryDMAuthController extends Controller
{
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required',
            'password' => 'required|min:6'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => Helpers::error_processor($validator)], 403);
        }

        $data = [
            'phone' => $request->phone,
            'password' => $request->password
        ];

        if (auth('delivery_men')->attempt($data)) {
            $token = \Illuminate\Support\Str::random(120);

            if(auth('delivery_men')->user()->application_status != 'approved')
            {
                return response()->json([
                    'errors' => [
                        ['code' => 'auth-003', 'message' => trans('messages.your_application_is_not_approved_yet')]
                    ]
                ], 401);
            }
            else if(!auth('delivery_men')->user()->status)
            {
                $errors = [];
                array_push($errors, ['code' => 'auth-003', 'message' => trans('messages.your_account_has_been_suspended')]);
                return response()->json([
                    'errors' => $errors
                ], 402);
            }

            if(!auth('delivery_men')->user()->is_laundry_dm){
                return response()->json([
                    'errors' => [
                        ['code' => 'auth-003', 'message' => trans('messages.you_do_not_belong_to_laundry_delivery_service')]
                    ]
                ], 401);
            }



            $delivery_man =  DeliveryMan::where(['phone' => $request['phone']])->where('is_laundry_dm', 1)->first();
            $delivery_man->auth_token = $token;
            $delivery_man->save();

            return response()->json(['token' => $token, 'zone_wise_topic'=>'zone_'.$delivery_man->zone_id.'_delivery_man'], 200);
        } else {
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }
    }
}
