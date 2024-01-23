@php($currency=\App\Models\BusinessSetting::where(['key'=>'currency'])->first()->value)

    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <title>
        @yield('title')
    </title>
    <!-- SEO Meta Tags-->
    <meta name="description" content="">
    <meta name="keywords" content="">
    <meta name="author" content="">
    <!-- Viewport-->
    {{--<meta name="_token" content="{{csrf_token()}}">--}}
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Favicon and Touch Icons-->
    <link rel="shortcut icon" href="favicon.ico">
    <!-- Font -->
    <!-- CSS Implementing Plugins -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/vendor.min.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/vendor/icon-set/style.css">
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/custom.css">
    <!-- CSS Front Template -->
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/theme.minc619.css?v=1.0">

    <style>
        .stripe-button-el {
            display: none !important;
        }

        .razorpay-payment-button {
            display: none !important;
        }
    </style>
    <script
        src="{{asset('public/assets/admin')}}/vendor/hs-navbar-vertical-aside/hs-navbar-vertical-aside-mini-cache.js"></script>
    <link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/toastr.css">
    {{--stripe--}}
    <script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
    <script src="https://js.stripe.com/v3/"></script>
    {{--stripe--}}
</head>
<!-- Body-->
<body class="toolbar-enabled">
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div id="loading" style="display: none;">
                <div style="position: fixed;z-index: 9999; left: 40%;top: 37% ;width: 100%">
                    <img width="200" src="{{asset('public/assets/admin/img/loader.gif')}}">
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Page Content-->
<div class="container pb-5 mb-2 mb-md-4">
    <div class="row">
        <div class="col-md-12 mb-5 pt-5">
            <center class="">
                <h1>Payment method</h1>
            </center>
        </div>
        @php($order=\App\Models\Order::find(session('order_id')))
        <section class="col-lg-12">
            <div class="checkout_details mt-3">
                <div class="row">

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('ssl_commerz_payment'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card" onclick="$('#ssl-form').submit()">
                                <div class="card-body pt-2" style="height: 70px">
                                    <form action="{{ url('/pay-ssl') }}" method="POST" class="needs-validation" id="ssl-form">
                                        <input type="hidden" value="{{ csrf_token() }}" name="_token"/>
                                        <button class="btn btn-block" type="submit">
                                            <img width="100"
                                                 src="{{asset('public/assets/admin/img/sslcomz.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pt-1" style="height: 70px">
                                    @php($config=\App\CentralLogics\Helpers::get_business_settings('razor_pay'))
                                    @php($order=\App\Models\Order::find(session('order_id')))
                                    <form action="{!!route('payment-razor')!!}" method="POST">
                                    @csrf
                                    <!-- Note that the amount is in paise = 50 INR -->
                                        <!--amount need to be in paisa-->
                                        <script src="https://checkout.razorpay.com/v1/checkout.js"
                                                data-key="{{ Config::get('razor.razor_key') }}"
                                                data-amount="{{$order->order_amount*100}}"
                                                data-buttontext="Pay {{$order->order_amount}} {{\App\CentralLogics\Helpers::currency_code()}}"
                                                data-name="{{\App\Models\BusinessSetting::where(['key'=>'business_name'])->first()->value}}"
                                                data-description="{{$order['id']}}"
                                                data-image="{{asset('storage/app/public/business/'.\App\Models\BusinessSetting::where(['key'=>'logo'])->first()->value)}}"
                                                data-prefill.name="{{$order->customer->f_name}}"
                                                data-prefill.email="{{$order->customer->email}}"
                                                data-theme.color="#ff7529">
                                        </script>
                                    </form>
                                    <button class="btn btn-block" type="button"
                                            onclick="{{\App\CentralLogics\Helpers::currency_code()=='INR'?"$('.razorpay-payment-button').click()":"toastr.error('Your currency is not supported by Razor Pay.')"}}">
                                        <img width="100"
                                             src="{{asset('public/assets/admin/img/razorpay.png')}}"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif


                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paypal'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pb-0 pt-1" style="height: 70px">
                                    <form class="needs-validation" method="POST" id="payment-form"
                                          action="{{route('pay-paypal')}}">
                                        {{ csrf_field() }}
                                        <button class="btn btn-block" type="submit">
                                            <img width="100"
                                                 src="{{asset('public/assets/admin/img/paypal.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif


                    @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body py-0" style="height: 70px">
                                    @php($config=\App\CentralLogics\Helpers::get_business_settings('stripe'))
                                    <button class="btn btn-block" type="button" id="checkout-button">
                                        <img width="100" src="{{asset('public/assets/admin/img/stripe.png')}}"/>
                                    </button>

                                    <script type="text/javascript">
                                        // Create an instance of the Stripe object with your publishable API key
                                        var stripe = Stripe('{{$config['published_key']}}');
                                        var checkoutButton = document.getElementById("checkout-button");
                                        checkoutButton.addEventListener("click", function () {
                                            fetch("{{route('pay-stripe')}}", {
                                                method: "GET",
                                            }).then(function (response) {
                                                console.log(response)
                                                return response.text();
                                            }).then(function (session) {
                                                console.log(JSON.parse(session).id)
                                                return stripe.redirectToCheckout({sessionId: JSON.parse(session).id});
                                            }).then(function (result) {
                                                if (result.error) {
                                                    alert(result.error.message);
                                                }
                                            }).catch(function (error) {
                                                console.error("Error:", error);
                                            });
                                        });
                                    </script>
                                </div>
                            </div>
                        </div>
                    @endif


                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paystack'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body py-0" style="height: 70px">
                                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paystack'))
                                    @php($order=\App\Models\Order::find(session('order_id')))
                                    <form method="POST" action="{{ route('paystack-pay') }}" accept-charset="UTF-8"
                                          class="form-horizontal"
                                          role="form">
                                        @csrf
                                        <div class="row">
                                            <div class="col-md-8 col-md-offset-2">
                                                <input type="hidden" name="email"
                                                       value="{{$order->customer->email!=null?$order->customer->email:'required@email.com'}}"> {{-- required --}}
                                                <input type="hidden" name="orderID" value="{{$order['id']}}">
                                                <input type="hidden" name="amount"
                                                       value="{{$order['order_amount']*100}}"> {{-- required in kobo --}}
                                                <input type="hidden" name="quantity" value="1">
                                                <input type="hidden" name="currency"
                                                       value="{{$currency}}">
                                                <input type="hidden" name="metadata"
                                                       value="{{ json_encode($array = ['key_name' => 'value',]) }}"> {{-- For other necessary things you want to add to your payload. it is optional though --}}
                                                <input type="hidden" name="reference"
                                                       value="{{ Paystack::genTranxRef() }}"> {{-- required --}}
                                                <p>
                                                    <button class="paystack-payment-button" style="display: none"
                                                            type="submit"
                                                            value="Pay Now!"></button>
                                                </p>
                                            </div>
                                        </div>
                                    </form>
                                    <button class="btn btn-block" type="button"
                                            onclick="$('.paystack-payment-button').click()">
                                        <img width="100"
                                             src="{{asset('public/assets/admin/img/paystack.png')}}"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('senang_pay'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pt-1" style="height: 70px">
                                    @php($config=\App\CentralLogics\Helpers::get_business_settings('senang_pay'))
                                    @php($order=\App\Models\Order::find(session('order_id')))
                                    @php($user=\App\Models\User::where(['id'=>$order['user_id']])->first())
                                    @php($secretkey = $config['secret_key'])
                                    @php($data = new \stdClass())
                                    @php($data->merchantId = $config['merchant_id'])
                                    @php($data->detail = 'payment')
                                    @php($data->order_id = $order->id)
                                    @php($data->amount = $order->order_amount)
                                    @php($data->name = $user->f_name.' '.$user->l_name)
                                    @php($data->email = $user->email)
                                    @php($data->phone = $user->phone)
                                    @php($data->hashed_string = md5($secretkey . urldecode($data->detail) . urldecode($data->amount) . urldecode($data->order_id)))

                                    <form name="order" method="post"
                                          action="https://{{env('APP_MODE')=='live'?'app.senangpay.my':'sandbox.senangpay.my'}}/payment/{{$config['merchant_id']}}">
                                        <input type="hidden" name="detail" value="{{$data->detail}}">
                                        <input type="hidden" name="amount" value="{{$data->amount}}">
                                        <input type="hidden" name="order_id" value="{{$data->order_id}}">
                                        <input type="hidden" name="name" value="{{$data->name}}">
                                        <input type="hidden" name="email" value="{{$data->email}}">
                                        <input type="hidden" name="phone" value="{{$data->phone}}">
                                        <input type="hidden" name="hash" value="{{$data->hashed_string}}">
                                    </form>

                                    <button class="btn btn-block" type="button"
                                            onclick="{{\App\CentralLogics\Helpers::currency_code()=='MYR'?"document.order.submit()":"toastr.error('Your currency is not supported by Senang Pay.')"}}">
                                        <img width="100"
                                             src="{{asset('public/assets/admin/img/senangpay.png')}}"/>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('flutterwave'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pt-2" style="height: 70px">
                                    <form method="POST" action="{{ route('flutterwave_pay') }}">
                                        {{ csrf_field() }}

                                        <button class="btn btn-block" type="submit">
                                            <img width="200"
                                                 src="{{asset('public/assets/admin/img/fluterwave.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('mercadopago'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pt-2" style="height: 70px">
                                    <a class="btn btn-block" href="{{route('mercadopago.index')}}">
                                        <img width="150"
                                                src="{{asset('public/assets/admin/img/MercadoPago_(Horizontal).svg')}}"/>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif


                    @php($config=\App\CentralLogics\Helpers::get_business_settings('paymob_accept'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 100px">
                                    <form class="needs-validation" method="POST" id="payment-form-paymob"
                                        action="{{route('paymob-credit')}}">
                                        {{ csrf_field() }}
                                        <button class="btn btn-block" type="submit">
                                            <img width="150"
                                                src="{{asset('public/assets/admin/img/paymob.png')}}"/>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif

                    @php($config=\App\CentralLogics\Helpers::get_business_settings('simpaisa'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body" style="height: 100px">
                                    <button class="btn btn-block" data-toggle="modal" data-target="#simpaisaModal">
                                        <img width="150"
                                            src="{{asset('public/assets/admin/img/simpaisa.png')}}"/>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" id="simpaisaModal">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content p-3">
                                    <a class="btn btn-secondary btn-lg btn-block text-capitalize" href="{{route('simpaisa-payment-card',['group_id'=>session('order_id'), 'payment_option'=>'card'])}}" ><i class="tio-credit-card mr-1"></i>{{__('messages.card')}} {{__('messages.payment')}}</a>
                                    <a class="btn btn-secondary btn-lg btn-block text-capitalize" data-toggle="collapse" href="#collapseSimpaisaWallet" role="button" aria-expanded="false" aria-controls="collapseSimpaisaWallet"><i class="tio-wallet mr-1"></i>{{__('messages.wallet')}} {{__('messages.payment')}}</a>
                                    <form action="javascript:" class="card card-body mt-1 p-2 collapse" id="collapseSimpaisaWallet" method="GET">
                                        <h3 class="text-center">{{__('messages.Simpaisa')}} {{__('messages.wallet')}}</h3>
                                        <div class="form-group">
                                            <label for="requiredLabel" class="input-label">
                                                {{__('messages.select')}} {{__('messages.operator')}}
                                            </label>
                                            <select name="operator_id" class="form-control">
                                                <option value="100001">MOBILINK - DCB</option>
                                                <option value="100002">TELENOR - DCB</option>
                                                <option value="100003">ZONG - DCB</option>
                                                <option value="100004">WARID - DCB</option>
                                                <option value="100005">UFONE - DCB</option>
                                                <option value="100007">EASYPAISA - WALLETS</option>
                                                <option value="100008">JAZZ CASH - WALLETS</option>
                                                <option value="100010">CARD - BANKING</option>
                                                <option value="100011">1LINK - E-BANKING</option>
                                                <option value="100012">ALFA - WALLETS</option>
                                                <option value="100014">HBL-KONNECT - WALLETS</option>
                                            </select>
                                        </div>
                                        <div class="form-group">
                                            <label for="requiredLabel" class="input-label">
                                                {{__('messages.MSISDN')}}
                                            </label>
                                            <input type="tel"  name="msisdn"  class="form-control" required>
                                        </div>
                                        <input type="hidden" value="{{session('data')['phone']}}" name="phone">
                                        <div class="form-group text-center p-0 m-0">
                                            <button type="submit" class="btn btn-primary text-capitalize">{{__('messages.Submit')}}</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="modal" tabindex="-1" role="dialog" aria-labelledby="simpaisaOtpModalLabel" aria-hidden="true" id="simpaisaOtpModal">
                            <div class="modal-dialog modal-sm modal-dialog-centered">
                                <div class="modal-content p-3">
                                    <form action="{{route('simpaisa-verify-wallet-transaction')}}" method="GET" class="card card-body mt-1 p-2" method="GET">
                                        <h3 class="text-center">{{__('messages.Simpaisa')}} {{__('messages.wallet')}}</h3>
                                        <div class="form-group">
                                            <label for="requiredLabel" class="input-label">
                                                {{__('messages.enter_otp')}}
                                            </label>
                                            <input type="text"  name="otp"  class="form-control" required>
                                            <input type="hidden" name="userKey" id="simpaisa_user_key">
                                            <input type="hidden" name="operatorId" id="simpaisa_operator_id">
                                            <input type="hidden" name="msisdn" id="simpaisa_msisdn">
                                            <input type="hidden" name="group_id" id="simpaisa_group_id">
                                        </div>
                                        <div class="form-group text-center p-0 m-0">
                                            <button type="submit" class="btn btn-primary text-capitalize">{{__('messages.Submit')}}</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    @endif
                    @php($config=\App\CentralLogics\Helpers::get_business_settings('payfast'))
                    @if($config['status'])
                        <div class="col-md-6 mb-4" style="cursor: pointer">
                            <div class="card">
                                <div class="card-body pt-2" style="height: 70px">
                                    <a class="btn btn-block" href="{{route('payfast-payment',request()->getQueryString())}}">
                                        <img width="150"
                                                src="{{asset('public/assets/admin/img/payfast.png')}}"/>
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </section>
    </div>
</div>

<!-- JS Front -->
<script src="{{asset('public/assets/admin')}}/js/custom.js"></script>
<script src="{{asset('public/assets/admin')}}/js/vendor.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/theme.min.js"></script>
<script src="{{asset('public/assets/admin')}}/js/sweet_alert.js"></script>
<script src="{{asset('public/assets/admin')}}/js/toastr.js"></script>
<script src="{{asset('public/assets/admin')}}/js/bootstrap.min.js"></script>

{!! Toastr::message() !!}



<script>
    setTimeout(function () {
        $('.stripe-button-el').hide();
        $('.razorpay-payment-button').hide();
    }, 10)

    $('#collapseSimpaisaWallet').on('submit', function (e) {
        e.preventDefault();
        var formData = new FormData(this);
        $.post({
            url: "{{route('simpaisa-payment-wallet')}}?group_id={{session('order_id')}}",
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            beforeSend: function () {
                $('#simpaisaModal').modal('hide');
                $('#loading').show();
            },
            success: function (data) {
                if (data.errors) {
                    for (var i = 0; i < data.errors.length; i++) {
                        toastr.error(data.errors[i].message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                } else {
                    $('#loading').hide();
                    $('#simpaisaOtpModal').modal('show');
                    $('#simpaisa_user_key').val(data.userKey);
                    $('#simpaisa_operator_id').val(data.operatorId);
                    $('#simpaisa_msisdn').val(data.msisdn);
                    $('#simpaisa_group_id').val(data.group_id);
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                toastr.error("{{trans('messages.config_your_account',['method'=>trans('messages.Simpaisa')])}}", {
                    CloseButton: true,
                    ProgressBar: true
                });
            },
            complete: function () {
                $('#loading').hide();
            },
        });
    })
</script>

</body>
</html>
