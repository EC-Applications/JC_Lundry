<?php

use App\CentralLogics\Helpers;
use App\Models\Order;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', 'HomeController@index')->name('home');
Route::get('terms-and-conditions', 'HomeController@terms_and_conditions')->name('terms-and-conditions');
Route::get('about-us', 'HomeController@about_us')->name('about-us');
Route::get('contact-us', 'HomeController@contact_us')->name('contact-us');
Route::get('privacy-policy', 'HomeController@privacy_policy')->name('privacy-policy');

Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthenticated.']);
    return response()->json([
        'errors' => $errors,
    ], 401);
})->name('authentication-failed');

Route::group(['prefix' => 'payment-mobile'], function () {
    Route::get('/', 'PaymentController@payment')->name('payment-mobile');
    Route::get('set-payment-method/{name}', 'PaymentController@set_payment_method')->name('set-payment-method');
});

// SSLCOMMERZ Start
/*Route::get('/example1', 'SslCommerzPaymentController@exampleEasyCheckout');
Route::get('/example2', 'SslCommerzPaymentController@exampleHostedCheckout');*/
Route::post('pay-ssl', 'SslCommerzPaymentController@index');
Route::post('/success', 'SslCommerzPaymentController@success');
Route::post('/fail', 'SslCommerzPaymentController@fail');
Route::post('/cancel', 'SslCommerzPaymentController@cancel');
Route::post('/ipn', 'SslCommerzPaymentController@ipn');
//SSLCOMMERZ END

/*paypal*/
/*Route::get('/paypal', function (){return view('paypal-test');})->name('paypal');*/
Route::post('pay-paypal', 'PaypalPaymentController@payWithpaypal')->name('pay-paypal');
Route::get('paypal-status', 'PaypalPaymentController@getPaymentStatus')->name('paypal-status');
/*paypal*/

/*Route::get('stripe', function (){
return view('stripe-test');
});*/

Route::get('pay-stripe', 'StripePaymentController@payment_process_3d')->name('pay-stripe');
Route::get('pay-stripe/success', 'StripePaymentController@success')->name('pay-stripe.success');
Route::get('pay-stripe/fail', 'StripePaymentController@fail')->name('pay-stripe.fail');

// Get Route For Show Payment Form
Route::get('paywithrazorpay', 'RazorPayController@payWithRazorpay')->name('paywithrazorpay');
Route::post('payment-razor', 'RazorPayController@payment')->name('payment-razor');

/*Route::fallback(function () {
return redirect('/admin/auth/login');
});*/

Route::get('payment-success', 'PaymentController@success')->name('payment-success');
Route::get('payment-fail', 'PaymentController@fail')->name('payment-fail');

//senang pay
Route::match(['get', 'post'], '/return-senang-pay', 'SenangPayController@return_senang_pay')->name('return-senang-pay');

// paymob
Route::post('/paymob-credit', 'PaymobController@credit')->name('paymob-credit');
Route::get('/paymob-callback', 'PaymobController@callback')->name('paymob-callback');

//paystack
Route::post('/paystack-pay', 'PaystackController@redirectToGateway')->name('paystack-pay');
Route::get('/paystack-callback', 'PaystackController@handleGatewayCallback')->name('paystack-callback');
Route::get('/paystack',function (){
    return view('paystack');
});


// The route that the button calls to initialize payment
Route::post('/flutterwave-pay','FlutterwaveController@initialize')->name('flutterwave_pay');
// The callback url after a payment
Route::get('/rave/callback', 'FlutterwaveController@callback')->name('flutterwave_callback');


// The callback url after a payment
Route::get('mercadopago/home', 'MercadoPagoController@index')->name('mercadopago.index');
Route::post('mercadopago/make-payment', 'MercadoPagoController@make_payment')->name('mercadopago.make_payment');
Route::get('mercadopago/get-user', 'MercadoPagoController@get_test_user')->name('mercadopago.get-user');

// paymob
Route::get('/simpaisa-payment-card', 'SimpaisaController@make_payment_card')->name('simpaisa-payment-card');
Route::any('/simpaisa-payment-wallet', 'SimpaisaController@make_payment_wallet')->name('simpaisa-payment-wallet');
Route::any('/simpaisa-callback', 'SimpaisaController@call_back')->name('simpaisa-callback');
Route::get('/simpaisa-verify-wallet-transaction', 'SimpaisaController@verify_wallet_transaction')->name('simpaisa-verify-wallet-transaction');

//PayFast
Route::get('payfast-payment', 'PayFastController@payment')->name('payfast-payment');
Route::any('payfast-callback', 'PayFastController@callback')->name('payfast-callback');

Route::get('/test',function (){
    // dd("Hello tester!");
    // $date_deb = '2021-08-21 19:20:53';
    // $get_min = date('i', strtotime($date_deb));
    // $interval = $datetime1->diff($datetime2);
    // echo date("g:i a", strtotime('+'.$get_min.'minutes',strtotime($date_deb)));

    // $datetime1 = new DateTime('2021-08-21 19:20:53');
    // $datetime2 = now();
    // $interval = $datetime1->diff($datetime2);
    // if($interval){
    //     echo 'Late Delivery';
    // }
    // echo $interval->format('%h')." Hours ".$interval->format('%i')." Minutes";

    // $status = array('cancel', 'failed', 'delivered');
    // if(in_array('pending', $status)){
    //     echo 'True';
    // }
    // $order = Order::find(100073);
    // Helpers::delivery_time_log(1, '2021-08-21', '10', $order->processing, $order->delivered, $order->id, $status = 'delivered');
    // dd(date('Y-m-d H:i:s'));
    // $status = array('canceled', 'failed', 'delivered');
    // if(in_array($order->order_status, $status)){
    //     echo "hello";
    // }

    DB::table('business_settings')->updateOrInsert(['key' => 'dm_max_cash_in_hand'], [
        'value' => 10000
    ]);



});

Route::get('authentication-failed', function () {
    $errors = [];
    array_push($errors, ['code' => 'auth-001', 'message' => 'Unauthorized.']);
    return response()->json([
        'errors' => $errors
    ], 401);
})->name('authentication-failed');

Route::get('module-test',function (){

});


//Restaurant Registration
Route::group(['prefix' => 'restaurant', 'as' => 'restaurant.'], function () {
    Route::get('apply', 'VendorController@create')->name('create');
    Route::post('apply', 'VendorController@store')->name('store');
});

//Deliveryman Registration
Route::group(['prefix' => 'deliveryman', 'as' => 'deliveryman.'], function () {
    Route::get('apply', 'DeliveryManController@create')->name('create');
    Route::post('apply', 'DeliveryManController@store')->name('store');
});
