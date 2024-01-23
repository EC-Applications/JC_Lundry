<!-- Body -->
<div class="card mt-5">
    <h4 class="card-header">
        {{__('messages.order_:')}} {{ $order->id }} {{__('messages.details')}}
    </h4>
    <div class="card-body">
        <?php
            $coupon = null;
            $total_addon_price = 0;
            $product_price = 0;
            $restaurant_discount_amount = 0;
            $del_c=$order['delivery_charge'];

            if($order->coupon_code)
            {
                $coupon = \App\Models\Coupon::where(['code' => $order['coupon_code']])->first();
                if($coupon->coupon_type == 'free_delivery')
                {
                    $del_c = 0;
                    $coupon = null;
                }
            }
            $details = $order->details;

        ?>

        @php($previous_service_id = null)
        @foreach($details as $key=>$detail)
            @php($service_id = $detail['services_id'])
            @php($laundry_item = $detail['laundry_item'])

            <!-- Check if the service_id is the same as the previous one -->
            @if ($previous_service_id != $service_id)
                <div class="row">
                    <div class="col-12">
                        <h4>{{ $detail->service->name }}</h6>
                    </div>
                </div>
            @endif

            <!-- Display the laundry_item details -->
            @if($detail->laundry_item)
                <!-- Media -->
                <div class="media">
                    <a class="avatar avatar-xl mr-3" href="{{route('admin.laundry.item.edit', $detail->laundry_item['id'])}}">
                        <img class="img-fluid"
                                src="{{asset('/storage/app/public/laundry-items')}}/{{$detail->laundry_item['icon']}}"
                                onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'"
                                alt="Image Description">
                    </a>
                    <div class="media-body">
                        <div class="row">
                            <div class="col-md-3 mb-3 mb-md-0">
                                <strong> {{Str::limit($detail->laundry_item['name'], 20, '...')}}</strong><br>
                            </div>

                            <div class="col col-md-3 align-self-center">
                                <h6>{{\App\CentralLogics\Helpers::format_currency($detail['price'])}}</h6>
                            </div>
                            <div class="col col-md-3 align-self-center">
                                <h5>{{$detail['quantity']}}</h5>
                            </div>

                            <div class="col col-md-3 align-self-center text-right">
                                @php($amount=($detail['price'])*$detail['quantity'])
                                <h5>{{\App\CentralLogics\Helpers::format_currency($amount)}}</h5>
                            </div>
                        </div>
                    </div>
                </div>
                @php($product_price+=$amount)
                {{-- @php($restaurant_discount_amount += ($detail['discount_on_food']*$detail['quantity'])) --}}
                <!-- End Media -->
                <hr>
                @php($previous_service_id = $service_id)
            @endif

        @endforeach
        <?php
            $discount_amount = $order['discount_amount'];

            $total_price = $product_price - $discount_amount;

            $tax_amount= $order['tax_amount'];

        ?>

        <div class="row justify-content-md-end mb-3">
            <div class="col-md-9 col-lg-8">
                <dl class="row text-sm-right">
                    <dt class="col-sm-6">{{__('messages.items')}} {{__('messages.price')}}:</dt>
                    <dd class="col-sm-6">
                        {{\App\CentralLogics\Helpers::format_currency($product_price)}}
                        <hr>
                    </dd>

                    <dt class="col-sm-6">{{__('messages.subtotal')}}:</dt>
                    <dd class="col-sm-6">
                        {{\App\CentralLogics\Helpers::format_currency($product_price)}}</dd>
                    {{-- <dt class="col-sm-6">{{__('messages.discount')}}:</dt>
                    <dd class="col-sm-6">
                        - {{\App\CentralLogics\Helpers::format_currency($restaurant_discount_amount)}}</dd> --}}
                    <dt class="col-sm-6">{{__('messages.coupon')}} {{__('messages.discount')}}:</dt>
                    <dd class="col-sm-6">
                        - {{\App\CentralLogics\Helpers::format_currency($discount_amount)}}</dd>
                    <dt class="col-sm-6">{{__('messages.vat/tax')}}:</dt>
                    <dd class="col-sm-6">
                        + {{\App\CentralLogics\Helpers::format_currency($tax_amount)}}</dd>
                    <dt class="col-sm-6">{{__('messages.delivery')}} {{__('messages.fee')}}:</dt>
                    <dd class="col-sm-6">
                        + {{\App\CentralLogics\Helpers::format_currency($del_c)}}
                        <hr>
                    </dd>

                    <dt class="col-sm-6">{{__('messages.total')}}:</dt>
                    <dd class="col-sm-6">{{\App\CentralLogics\Helpers::format_currency($product_price+$del_c+$tax_amount - $discount_amount)}}</dd>
                </dl>
                <!-- End Row -->
            </div>
            {{-- @if($editing)
            <div class="offset-sm-8 col-sm-4 d-flex justify-content-between">
                <button class="btn btn-sm btn-danger" type="button" onclick="cancle_editing_order()">{{__('messages.cancel')}}</button>
                <button class="btn btn-sm btn-primary" type="button" onclick="update_order()">{{__('messages.submit')}}</button>
            </div>
            @endif --}}
        </div>
            <!-- End Row -->
    </div>
</div>
<!-- End Body -->
