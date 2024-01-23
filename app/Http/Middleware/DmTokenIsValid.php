<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\DeliveryMan;
use Illuminate\Http\Request;
use App\CentralLogics\Helpers;
use Illuminate\Support\Facades\Validator;

class DmTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if(!$dm = DeliveryMan::where("auth_token", $request->token)->whereNotNull('auth_token')->first()){
            $errors = [];
            array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
            return response()->json([
                'errors' => $errors
            ], 401);
        }elseif($dm->status == 0){
            $errors = [];
            array_push($errors, ['code' => 'auth-003', 'message' => trans('messages.your_account_has_been_suspended')]);
            return response()->json([
                'errors' => $errors
            ], 401);
        }

        return $next($request);
    }
}
