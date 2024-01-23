<?php

namespace App\Http\Controllers;

use App\CentralLogics\Helpers;
use App\Models\Order;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Common\PayPalModel;
use PayPal\Rest\ApiContext;

class PaypalPaymentController extends Controller
{
    public function __construct()
    {
        $paypal_conf = Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    public function payWithpaypal(Request $request)
    {
        $price = Order::whereIn('id', session('order_id'))->sum('order_amount');
        $tr_ref = Str::random(6) . '-' . rand(1, 1000);

        $payer = new Payer();
        $payer->setPaymentMethod('paypal');

        $items_array = [];
        $item = new Item();
        $item->setName(session('data')['name'])
            ->setCurrency(Helpers::currency_code())
            ->setQuantity(1)
            ->setPrice($price);
        array_push($items_array, $item);

        $item_list = new ItemList();
        $item_list->setItems($items_array);

        $amount = new Amount();
        $amount->setCurrency(Helpers::currency_code())
            ->setTotal($price);

        \session()->put('transaction_reference', $tr_ref);
        $transaction = new Transaction();
        $transaction->setAmount($amount)
            ->setItemList($item_list)
            ->setDescription($tr_ref);

        $redirect_urls = new RedirectUrls();
        $redirect_urls->setReturnUrl(URL::route('paypal-status'))
        ->setCancelUrl(URL::route('payment-fail'));

        $payment = new Payment();
        $payment->setIntent('Sale')
            ->setPayer($payer)
            ->setRedirectUrls($redirect_urls)
            ->setTransactions(array($transaction));
        try {
            $payment->create($this->_api_context);

            foreach ($payment->getLinks() as $link) {
                if ($link->getRel() == 'approval_url') {
                    $redirect_url = $link->getHref();
                    break;
                }
            }

            DB::table('orders')
                ->whereId('id',session('order_id'))
                ->update([
                    'transaction_reference' => $payment->getId(),
                    'payment_method' => 'paypal',
                    'order_status' => 'failed',
                    'failed' => now(),
                    'updated_at' => now()
                ]);

            Session::put('paypal_payment_id', $payment->getId());
            if (isset($redirect_url)) {
                return Redirect::away($redirect_url);
            }

        } catch (\Exception $ex) {
            Toastr::error(trans('messages.your_currency_is_not_supported',['method'=>trans('messages.paypal')]));
            return back();
        }

        Session::put('error', trans('messages.config_your_account',['method'=>trans('messages.paypal')]));
        return back();
    }

    public function getPaymentStatus(Request $request)
    {
        $payment_id = Session::get('paypal_payment_id');
        if (empty($request['PayerID']) || empty($request['token'])) {
            Session::put('error', trans('messages.payment_failed'));
            return Redirect::back();
        }

        $payment = Payment::get($payment_id, $this->_api_context);
        $execution = new PaymentExecution();
        $execution->setPayerId($request['PayerID']);

        /**Execute the payment **/
        $result = $payment->execute($execution, $this->_api_context);
        $orders = Order::whereIn('id', session('order_id'))->get();
        if ($result->getState() == 'approved') {
            foreach($orders as $order)
            {
                $order->transaction_reference = $payment_id;
                $order->payment_method = 'paypal';
                $order->payment_status = 'paid';
                $order->order_status = 'confirmed';
                $order->confirmed = now();
                $order->save();
                try {
                    Helpers::send_order_notification($order);
                } catch (\Exception $e) {
                }



                if (session()->has('callback')) {
                    return redirect(session('callback') . '&status=success');
                }else{
                    return \redirect()->route('payment-success');
                }
            }

        }
        foreach($orders as $order)
        {
            $order->order_status = 'failed';
            $order->failed = now();
            $order->save();
        }

        if (session()->has('callback') != null) {
            return redirect(session('callback') . '&status=fail');
        }else{
            return \redirect()->route('payment-fail');
        }
    }
}
