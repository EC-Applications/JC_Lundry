<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\CentralLogics\Helpers;
use App\CentralLogics\OrderLogic;

class SimpaisaController extends Controller
{
    private $url_card;
    private $url_wallet;
    private $config;
    private $currency;

    public function __construct()
    {
        $this->currency = Helpers::currency_code();

        if(env('APP_MODE') == 'live') {
            $this->url_card = 'https://payment.simpaisa.com';
            $this->url_wallet = 'https://wallets.simpaisa.com';
        } else {
            $this->url_card = 'https://staging.simpaisa.com';
            $this->url_wallet = 'https://staging.simpaisa.com';
        }

        $this->config = Helpers::get_business_settings('simpaisa');

    }

    public function make_payment_card(Request $request)
    {
        if($this->currency != 'PKR'){
            Toastr::error(trans('messages.your_currency_is_not_supported',['method'=>trans('messages.Simpaisa')]));
            return back();            
        }
        
        try{
            $response = Http::get($this->url_card.'/card/ubl/registrationfull', [
                'OrderId' => $request->group_id,
                'Amount' => Order::where('group_id', $request->group_id)->sum('order_amount'),
                'Currency'=> $this->currency,
                'MerchantId'=>$this->config['merchant_id'],
                'RedirectUrl'=>route('simpaisa-callback').'?group_id='.$request->group_id
            ]);

            echo($response->body());                 
           
        } catch (\Exception $ex){
            info($ex);
        }
        Toastr::error(trans('messages.config_your_account',['method'=>trans('messages.Simpaisa')]));
        return back();
    }

    public function make_payment_wallet(Request $request)
    {
        if($this->currency != 'PKR'){
            return response()->json(['errors' => [
                ['code'=>'currency', 'message'=>trans('messages.your_currency_is_not_supported',['method'=>trans('messages.Simpaisa')])]
            ]]);          
        }
        
        try{
            $response = Http::acceptJson()->post($this->url_wallet.'/v2/wallets/transaction/initiate', [
                "merchantId"=> $this->config['merchant_id'],
                "operatorId"=> $request->operator_id,
                "userKey"=> $request->phone,
                "msisdn"=> $request->msisdn,
                "transactionType"=> "0",
                "amount"=> Order::where('group_id', $request->group_id)->sum('order_amount'),
                "productReference"=> $request->group_id,
            ]);
            $data = $response->json();
            if(isset($data['status']) && $data['status'] == '000') {
                return response()->json(['userKey'=>$request->phone,"msisdn"=> $request->msisdn,"operatorId"=> $request->operator_id,'group_id'=>$request->group_id]);
            }    
           
        } catch (\Exception $ex){
            info($ex);
        }
        return response()->json(['errors' => [
            ['code'=>'config', 'message'=>trans('messages.config_your_account',['method'=>trans('messages.Simpaisa')])]
        ]]);

    }

    public function call_back(Request $request)
    {
        $response = Http::acceptJson()->post($this->url_card.'/card/finalize', [
            "orderId" => $request->group_id,
            "merchantId" => $this->config['merchant_id']
        ]);

        $data = $response->json();
        $callback = Order::where('group_id', $request->group_id)->first()->callback;
        
        if(isset($data['transactionId'])) {
            try{
                OrderLogic::update_order_status($request->group_id, 'simpaisa', 'confirmed', 'paid', $data['transactionId']);
            } catch (\Exception $ex) {
                info($ex);
                OrderLogic::update_order_status($request->group_id, 'simpaisa', 'failed', 'unpaid', $data['transactionId']);
                return redirect()->route('payment-fail', ['callback_url'=>$callback]);
            }
            return redirect()->route('payment-success',['callback_url'=>$callback]);
        } 
        OrderLogic::update_order_status($request->group_id, 'simpaisa', 'failed', 'unpaid');
        return redirect()->route('payment-fail', ['callback_url'=>$callback]);
    }

    
    public function verify_wallet_transaction(Request $request)
    {
        $response = Http::acceptJson()->post($this->url_wallet.'/v2/wallets/transaction/verify', [
            "merchantId" => $this->config['merchant_id'],
            "amount"=> Order::where('group_id', $request->group_id)->sum('order_amount'),
            "msisdn"=> $request->msisdn,
            "transactionType"=> "0",
            "userKey"=> $request->userKey,
            "operatorId"=> $request->operatorId,
            "otp"=> $request->otp
        ]);

        $data = $response->json();
        $callback = Order::where('group_id', $request->group_id)->first()->callback;
        
        if(isset($data['transactionId']) && $data['status'] == '000') {
            try{
                OrderLogic::update_order_status($request->group_id, 'simpaisa', 'confirmed', 'paid', $data['transactionId']);
            } catch (\Exception $ex) {
                info($ex);
                OrderLogic::update_order_status($request->group_id, 'simpaisa', 'failed', 'unpaid', $data['transactionId']);
                return redirect()->route('payment-fail', ['callback_url'=>$callback]);
            }
            return redirect()->route('payment-success',['callback_url'=>$callback]);
        } 
        OrderLogic::update_order_status($request->group_id, 'simpaisa', 'failed', 'unpaid');
        return redirect()->route('payment-fail', ['callback_url'=>$callback]);
    }
}
