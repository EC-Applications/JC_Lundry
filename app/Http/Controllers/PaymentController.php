<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {
        if ($request->has('callback')) {
            Order::where(['group_id' => $request->group_id])->update(['callback' => $request['callback']]);
        }

        session()->put('customer_id', $request['customer_id']);
        session()->put('order_id', $request->group_id);

        $customer = User::find($request['customer_id']);

        $order = Order::where('group_id', $request->group_id)->where('user_id', $request['customer_id'])->first();

        if (isset($customer) && isset($order)) {
            $data = [
                'name' => $customer['f_name'],
                'email' => $customer['email'],
                'phone' => $customer['phone'],
            ];
            session()->put('data', $data);
            return view('payment-view');
        }

        return response()->json(['errors' => ['code' => 'order-payment', 'message' => 'Data not found']], 403);
    }

    public function success(Request $request)
    {
        if (isset($request->callback_url)) {
            return redirect($request->callback_url . '&status=success');
        }
        return response()->json(['message' => 'Payment succeeded'], 200);
    }

    public function fail(Request $request)
    {
        if (isset($request->callback_url)) {
            return redirect($request->callback_url . '&status=fail');
        }
        return response()->json(['message' => 'Payment failed'], 403);
    }
}
