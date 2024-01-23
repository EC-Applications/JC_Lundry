<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\Order;
use Illuminate\Http\Request;

class PayFastController extends Controller
{
    public function payment(Request $request)
    {
        $config = Helpers::get_business_settings('payfast');
        $merchant_id=$config['merchant_id'];
        $secured_key=$config['secured_key'];
        $order=Order::where('group_id',$request->group_id)->first();
        $order_amount = Order::where('group_id', $request->group_id)->sum('order_amount');
        $trans_amount= $order_amount;
        $basket_id= $request->group_id;
        $tokenApiUrl ='https://ipguat.apps.net.pk/Ecommerce/api/Transaction/GetAccessToken';
        // $config['mode']=="test" ? 'https://apipxyuat.apps.net.pk:8443/api/Transaction/GetAccessToken':

        $urlPostParams = sprintf(
            'MERCHANT_ID=%s&SECURED_KEY=%s&TXNAMT=%s&BASKET_ID=%s',
            $merchant_id,
            $secured_key,
            $trans_amount,
            $basket_id
        );

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $tokenApiUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $urlPostParams);
        curl_setopt($ch, CURLOPT_USERAGENT, 'CURL');
        $response = curl_exec($ch);
        curl_close($ch);
        $payload = json_decode($response,true);
        // dd($payload);
        $token = isset($payload['ACCESS_TOKEN']) ? $payload['ACCESS_TOKEN'] : '';
        $requestParams = array(
            'MERCHANT_ID' => $merchant_id,
            'Merchant_Name' => $config['merchant_name'] ? $config['merchant_name'] : Helpers::get_business_settings('business_name'),
            'TOKEN' => $token,
            'PROCCODE' => 00,
            'TXNAMT' => $order_amount,
            'CUSTOMER_MOBILE_NO' => $order->customer->phone,
            'CUSTOMER_EMAIL_ADDRESS' => $order->customer->email,
            'SIGNATURE' => bin2hex(random_bytes(6)).'-'.$request->group_id,
            'VERSION' => 'MERCHANT-CART-0.1',
            'TXNDESC' =>'payfast payment',
            'SUCCESS_URL' => route('payfast-callback'),
            'FAILURE_URL' => route('payfast-callback'),
            'BASKET_ID' =>$request->group_id,
            'ORDER_DATE' =>$order->created_at,
            'CHECKOUT_URL'=>  route('payfast-callback'),


        );
        // dd($requestParams);
        $redirectUrl='https://ipguat.apps.net.pk/Ecommerce/api/Transaction/PostTransaction';
        return view('payfast',compact(['requestParams','redirectUrl']));
    }

    public function callback(Request $request)
    {
         info($request->all());
        $orders=Order::where('group_id',$request->basket_id)->get();
        if($orders && $request->err_code=="000")
          {
            foreach($orders as $order)
              {
                  $order->transaction_reference = $request->transaction_id;
                  $order->payment_method = 'payfast';
                  $order->payment_status = 'paid';
                  $order->order_status = 'confirmed';
                  $order->confirmed = now();
                  $order->save();

               }
                Helpers::send_order_notification($order);
                if ($order->callback != null) {
                    return redirect($order->callback . '&status=success');
                }else{
                    return \redirect()->route('payment-success');
                }
         }
         else{
            foreach($orders as $order)
            {
            $order->order_status = 'failed';
            $order->failed = now();
            $order->save();
           }
           if ($order->callback != null) {
            return redirect($order->callback . '&status=fail');
        }else{
            return \redirect()->route('payment-fail');
        }
        }

    }


}
