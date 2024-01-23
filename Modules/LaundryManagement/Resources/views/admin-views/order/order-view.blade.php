@extends('layouts.admin.app')

@section('title','Order Details')

@push('css_or_js')
<style>
    .item-box{
        height:250px;
        width:150px;
        padding:3px;
    }

    .header-item{
        width:10rem;
    }
    .barcode_image {
            display: block;
            margin-left: 35px;
        }
    .barcode_code {
        display: block;
        font-weight: bold;
    }

    .barcodea4 .item {
        display: block;
        overflow: hidden;
        text-align: center;
        border: 1px dotted #CCC;
        font-size: 12px;
        line-height: 14px;
        text-transform: uppercase;
        float: left;
        margin-right: 5px;
        margin-bottom: 8px;
    }
    .barcodea4 .style24 {
        width: 4.48in;
        /* height: 1.335in; */
        margin-left: 0.079in;
        padding-top: 0.05in;
    }
</style>
@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header d-print-none">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb breadcrumb-no-gutter">
                            <li class="breadcrumb-item">
                                <a class="breadcrumb-link"
                                   href="{{route('admin.order.list',['status'=>'all'])}}">
                                    {{__('messages.orders')}}
                                </a>
                            </li>
                            <li class="breadcrumb-item active" aria-current="page">{{__('messages.order')}} {{__('messages.details')}}</li>
                        </ol>
                    </nav>

                    {{-- order and payment status --}}
                    <div class="d-sm-flex align-items-sm-center">
                        <h1 class="page-header-title">{{__('messages.laundry')}} {{__('messages.order')}} #{{$laundry_order['id']}}</h1>

                        @if($laundry_order['payment_status']=='paid')
                            <span class="badge badge-soft-success ml-sm-3">
                                <span class="legend-indicator bg-success"></span>{{__('messages.paid')}}
                            </span>
                        @else
                            <span class="badge badge-soft-danger ml-sm-3">
                                <span class="legend-indicator bg-danger"></span>{{__('messages.unpaid')}}
                            </span>
                        @endif

                        @if($laundry_order['order_status']=='pending')
                            <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-info text"></span>{{__('messages.pending')}}
                            </span>
                        @elseif($laundry_order['order_status']=='confirmed')
                            <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-info"></span>{{__('messages.confirmed')}}
                            </span>
                        @elseif($laundry_order['order_status']=='out_for_pickup')
                            <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-info"></span>{{__('messages.out_for_pickup')}}
                            </span>
                        @elseif($laundry_order['order_status']=='picked_up')
                            <span class="badge badge-soft-info ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-info"></span>{{__('messages.picked_up')}}
                            </span>
                        @elseif($laundry_order['order_status']=='processing')
                            <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-warning"></span>{{__('messages.processing')}}
                            </span>
                        @elseif($laundry_order['order_status']=='out_for_delivery')
                            <span class="badge badge-soft-warning ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-warning"></span>{{__('messages.out_for_delivery')}}
                            </span>
                        @elseif($laundry_order['order_status']=='delivered')
                            <span class="badge badge-soft-success ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-success"></span>{{__('messages.delivered')}}
                            </span>
                        @elseif($laundry_order['order_status']=='failed')
                            <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                                <span class="legend-indicator text-capitalize bg-danger"></span>{{__('messages.payment')}} {{ __('messages.failed')}}
                            </span>
                        @else
                            <span class="badge badge-soft-danger ml-2 ml-sm-3 text-capitalize">
                              <span class="legend-indicator bg-danger"></span>{{str_replace('_',' ',$laundry_order['order_status'])}}
                            </span>
                        @endif

                        <span class="ml-2 ml-sm-3">
                                <i class="tio-date-range"></i> {{date('d M Y '.config('timeformat'),strtotime($laundry_order['created_at']))}}
                        </span>
                    </div>
                        @if($laundry_order->order_status != 'failed' && $laundry_order->order_status != 'delivered')
                            <div class="hs-unfold float-right">
                                <div class="dropdown">
                                    <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button"
                                            id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true"
                                            aria-expanded="false">
                                        {{__('messages.status')}}
                                    </button>
                                    {{-- @php($order_delivery_verification = (boolean)\App\Models\BusinessSetting::where(['key' => 'order_delivery_verification'])->first()->value) --}}
                                    <div class="dropdown-menu text-capitalize" aria-labelledby="dropdownMenuButton">
                                        <a class="dropdown-item {{$laundry_order['order_status']=='pending'?'active':''}}"
                                        onclick="route_alert('{{route('admin.laundry.order.status',['id'=>$laundry_order['id'],'order_status'=>'pending'])}}','Change status to pending ?')"
                                        href="javascript:">{{__('messages.pending')}}</a>
                                        <a class="dropdown-item {{$laundry_order['order_status']=='confirmed'?'active':''}}"
                                        onclick="route_alert('{{route('admin.laundry.order.status',['id'=>$laundry_order['id'],'order_status'=>'confirmed'])}}','Change status to confirmed ?')"
                                        href="javascript:">{{__('messages.confirmed')}}</a>
                                        <a class="dropdown-item {{$laundry_order['order_status']=='out_for_pickup'?'active':''}}"
                                        onclick="route_alert('{{route('admin.laundry.order.status',['id'=>$laundry_order['id'],'order_status'=>'out_for_pickup'])}}','Change status to out for out for pickup ?')"
                                        href="javascript:">{{__('messages.out_for_pickup')}}</a>
                                        <a class="dropdown-item {{$laundry_order['order_status']=='picked_up'?'active':''}}"
                                        onclick="route_alert('{{route('admin.laundry.order.status',['id'=>$laundry_order['id'],'order_status'=>'picked_up'])}}','Change status to out for pickup ?')"
                                        href="javascript:">{{__('messages.picked_up')}}</a>

                                        <a class="dropdown-item {{$laundry_order['order_status']=='arrived'?'active':''}}"
                                        onclick="route_alert('{{route('admin.laundry.order.status',['id'=>$laundry_order['id'],'order_status'=>'arrived'])}}','Change status to arrived ?')"
                                        href="javascript:">{{__('messages.arrived')}}</a>
                                        <a class="dropdown-item {{ $laundry_order['order_status'] == 'processing' ? 'active' : '' }}"
                                        onclick="route_alert('{{ route('admin.laundry.order.status', ['id' => $laundry_order['id'], 'order_status' => 'processing']) }}','Change status to processing ?')"
                                        href="javascript:">{{__('messages.processing')}}</a>
                                        <a class="dropdown-item {{$laundry_order['order_status']=='ready_for_delivery'?'active':''}}"
                                        onclick="route_alert('{{route('admin.laundry.order.status',['id'=>$laundry_order['id'],'order_status'=>'ready_for_delivery'])}}','Change status to ready for delivery ?')"
                                        href="javascript:">{{__('messages.ready_for_delivery')}}</a>
                                        <a class="dropdown-item {{$laundry_order['order_status']=='out_for_delivery'?'active':''}}"
                                        onclick="route_alert('{{route('admin.laundry.order.status',['id'=>$laundry_order['id'],'order_status'=>'out_for_delivery'])}}','Change status to out for delivery ?')"
                                        href="javascript:">{{__('messages.out_for_delivery')}}</a>
                                        <a class="dropdown-item {{$laundry_order['order_status']=='delivered'?'active':''}}"
                                        onclick="route_alert('{{route('admin.laundry.order.status',['id'=>$laundry_order['id'],'order_status'=>'delivered'])}}','Change status to delivered (payment status will be paid if not)?')"
                                        href="javascript:">{{__('messages.delivered')}}</a>
                                        <a class="dropdown-item {{$laundry_order['order_status']=='cancelled'?'active':''}}"
                                        onclick="route_alert('{{route('admin.laundry.order.status',['id'=>$laundry_order['id'],'order_status'=>'cancelled'])}}','Change status to canceled ?')"
                                        href="javascript:">{{__('messages.canceled')}}</a>
                                    </div>
                                </div>
                            </div>
                        @endif
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle mr-1"
                       href="{{route('admin.laundry.order.details',[$laundry_order['id']-1])}}"
                       data-toggle="tooltip" data-placement="top" title="Previous order">
                        <i class="tio-arrow-backward"></i>
                    </a>
                    <a class="btn btn-icon btn-sm btn-ghost-secondary rounded-circle"
                       href="{{route('admin.laundry.order.details',[$laundry_order['id']+1])}}" data-toggle="tooltip"
                       data-placement="top" title="Next order">
                        <i class="tio-arrow-forward"></i>
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row" id="printableArea">
            <div class="col-lg-8 mb-3 mb-lg-0">
                <!-- Card -->
                <div class="card mb-3 mb-lg-5">
                    <!-- Header -->
                    <div class="card-header" style="display: block!important;">
                        <div class="row">
                            <div class="col-12 pb-2 border-bottom  d-flex justify-content-between">
                                <h4 class="card-header-title">
                                    {{__('messages.order')}} {{__('messages.details')}}
                                    <span class="badge badge-soft-dark rounded-circle ml-1">{{$laundry_order->details->count()}}</span>
                                </h4>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6 pt-2">
                                <h6 style="color: #8a8a8a;">
                                    {{__('messages.order')}} {{__('messages.note')}} : {{$laundry_order['note']}}
                                </h6>
                            </div>
                            <div class="col-6 pt-2">
                                <div class="text-right">
                                    <h6 class="text-capitalize" style="color: #8a8a8a;">
                                        {{__('messages.payment')}} {{__('messages.method')}} : {{str_replace('_',' ',$laundry_order['payment_method'])}}
                                    </h6>
                                    <h6 class="" style="color: #8a8a8a;">
                                        @if($laundry_order['transaction_reference']==null)
                                            {{__('messages.reference')}} {{__('messages.code')}} :
                                            <button class="btn btn-outline-primary btn-sm" data-toggle="modal"
                                                    data-target=".bd-example-modal-sm">
                                                {{__('messages.add')}}
                                            </button>
                                        @else
                                            {{__('messages.reference')}} {{__('messages.code')}} : {{$laundry_order['transaction_reference']}}
                                        @endif
                                    </h6>
                                    <h6 class="text-capitalize" style="color: #8a8a8a;">{{__('messages.order')}} {{__('messages.type')}}
                                        : <label style="font-size: 10px"
                                                 class="badge badge-soft-primary">{{ trans('messages.laundry') }}</label>
                                    </h6>
                                    {{-- @if($order->schedule_at && $order->scheduled)
                                    <h6 class="text-capitalize" style="color: #8a8a8a;">{{__('messages.scheduled_at')}}
                                        : <label style="font-size: 10px"
                                                 class="badge badge-soft-primary">{{date('d M Y '.config('timeformat'),strtotime($order['schedule_at']))}}</label>
                                    </h6>
                                    @endif
                                    @if($order->coupon)
                                    <h6 class="text-capitalize" style="color: #8a8a8a;">{{__('messages.coupon')}}
                                        : <label style="font-size: 10px"
                                                 class="badge badge-soft-primary">{{$order->coupon_code}} ({{__('messages.'.$order->coupon->coupon_type)}})</label>
                                    </h6>
                                    @endif --}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Header -->
                    <!-- Body -->
                    <div class="card-body">
                    <?php
                        $coupon = null;
                        $total_addon_price = 0;
                        $product_price = 0;
                        $restaurant_discount_amount = 0;
                        $del_c=$laundry_order['delivery_charge'];

                        if($laundry_order->coupon_code)
                        {
                            $coupon = \App\Models\Coupon::where(['code' => $laundry_order['coupon_code']])->first();
                            if($coupon->coupon_type == 'free_delivery')
                            {
                                $del_c = 0;
                                $coupon = null;
                            }
                        }
                        $details = $laundry_order->details;

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
                                        <div class="col col-md-2 align-self-center">
                                            <h5>{{$detail['quantity']}}</h5>
                                        </div>

                                        <div class="col col-md-2 align-self-center text-right">
                                            @php($amount=($detail['price'])*$detail['quantity'])
                                            <h5>{{\App\CentralLogics\Helpers::format_currency($amount)}}</h5>
                                        </div>
                                        <div class="col col-md-2 align-self-center text-right">
                                            <button class="btn btn-outline-success btn-sm" data-toggle="modal"
                                            data-target="#barcode{{$detail->id}}" title="{{__('messages.click_to_view_barcode')}}"><i class="tio-barcode"></i></button>
                                        </div>

                                    <!--Show barcode on Modal -->
                                        <div class="modal fade" id="barcode{{$detail->id}}" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel">
                                            <div class="modal-dialog modal-lg" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h4 class="modal-title" id="locationModalLabel">{{$detail->laundry_orders_id }}'s {{__('messages.barcode')}}</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        @foreach ( $detail->item_track as $track)
                                                            <div class="barcodea4">
                                                                <div class="item style24">
                                                                    <span class="barcode_image">{!! DNS1D::getBarcodeHTML($track->bar_code, "C128") !!}</span>
                                                                    <span
                                                                        class="barcode_code text-capitalize">CODE: {{$track->bar_code}}</span>
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <!-- End Modal -->
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
                        $discount_amount = $laundry_order['discount_amount'];

                        $total_price = $product_price - $discount_amount;

                        $tax_amount= $laundry_order['tax_amount'];

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
                    <!-- End Body -->
                </div>
                <!-- End Card -->
            </div>

            <div class="col-lg-4">
                <!-- Card -->
                <div class="card mb-2">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">{{__('messages.deliveryman')}}</h4>
                        @if(($laundry_order->delivery_man) && !isset($laundry_order->delivered))
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                {{__('messages.change_delivery_deliveryman')}}
                            </button>
                        @endif
                        @if(($laundry_order->pickup_delivery_man) && !isset($laundry_order->picked_up))
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal">
                                {{__('messages.change_pickup_deliveryman')}}
                            </button>
                        @endif
                    </div>
                    <!-- End Header -->

                    <!-- Body -->

                    <div class="card-body">

                        <!--- For Delivery DM Card ------->
                        @if(isset($laundry_order->pickup_delivery_man))
                            <div class="h6">
                                <i class="tio-security-on"> </i>{{__('messages.pickup_deliveryman')}}
                            </div>
                            <a class="media align-items-center  deco-none" href="{{route('admin.laundry.delivery-man.preview',[$laundry_order->pickup_delivery_man['id']])}}">
                                <div class="avatar avatar-circle mr-3">

                                        <img class="avatar-img" style="width: 75px"
                                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                        src="{{asset('storage/app/public/delivery-man/'.$laundry_order->pickup_delivery_man->image)}}"
                                        alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <span class="text-body text-hover-primary">{{$laundry_order->pickup_delivery_man['f_name'].' '.$laundry_order->pickup_delivery_man['l_name']}}</span><br>
                                    <span class="badge badge-ligh">{{$laundry_order->pickup_delivery_man->orders_count}} {{__('messages.orders_delivered')}}</span>
                                </div>
                            </a>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{__('messages.contact')}} {{__('messages.info')}}</h5>
                            </div>

                            <ul class="list-unstyled list-unstyled-py-2">
                                <li>
                                    <i class="tio-online mr-2"></i>
                                    {{$laundry_order->pickup_delivery_man['email']}}
                                </li>
                                <li>
                                    <a class="deco-none" href="tel:{{$laundry_order->pickup_delivery_man['phone']}}">
                                        <i class="tio-android-phone-vs mr-2"></i>
                                    {{$laundry_order->pickup_delivery_man['phone']}}</a>
                                </li>
                            </ul>

                            <hr>
                            @php($address=$laundry_order->dm_last_location)
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{__('messages.last')}} {{__('messages.location')}}</h5>
                            </div>
                            @if(isset($address))
                            <span class="d-block">
                                <a target="_blank"
                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                    <i class="tio-map"></i> {{$address['location']}}<br>
                                </a>
                            </span>
                            @else
                            <span class="d-block text-lowercase qcont">
                                {{__('messages.location').' '.__('messages.not_found')}}
                            </span>
                            @endif
                        @else
                            <div class="w-100 text-center">
                                <div class="hs-unfold">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" data-lat='21.03' data-lng='105.85'>
                                        {{__('messages.assign_pickup_delivery_man_manually')}}
                                    </button>
                                </div>
                            </div>
                        @endif
                        <hr>
                        <!--- For Delivery DM Card ------->
                        @if($laundry_order->delivery_man)
                            <div class="h6">
                                <i class="tio-security-on"> </i>{{__('messages.deliveryman_for_delivery')}}
                            </div>
                            <a class="media align-items-center  deco-none" href="{{route('admin.laundry.delivery-man.preview',[$laundry_order->delivery_man['id']])}}">
                                <div class="avatar avatar-circle mr-3">

                                        <img class="avatar-img" style="width: 75px"
                                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                        src="{{asset('storage/app/public/delivery-man/'.$laundry_order->delivery_man->image)}}"
                                        alt="Image Description">
                                </div>
                                <div class="media-body">
                                    <span class="text-body text-hover-primary">{{$laundry_order->delivery_man['f_name'].' '.$laundry_order->delivery_man['l_name']}}</span><br>
                                    <span class="badge badge-ligh">{{$laundry_order->delivery_man->orders_count}} {{__('messages.orders_delivered')}}</span>
                                </div>
                            </a>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{__('messages.contact')}} {{__('messages.info')}}</h5>
                            </div>

                            <ul class="list-unstyled list-unstyled-py-2">
                                <li>
                                    <i class="tio-online mr-2"></i>
                                    {{$laundry_order->delivery_man['email']}}
                                </li>
                                <li>
                                    <a class="deco-none" href="tel:{{$laundry_order->delivery_man['phone']}}">
                                        <i class="tio-android-phone-vs mr-2"></i>
                                    {{$laundry_order->delivery_man['phone']}}</a>
                                </li>
                            </ul>

                            <hr>
                            @php($address=$laundry_order->dm_last_location)
                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{__('messages.last')}} {{__('messages.location')}}</h5>
                            </div>
                            @if(isset($address))
                            <span class="d-block">
                                <a target="_blank"
                                    href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$address['latitude']}}+{{$address['longitude']}}">
                                    <i class="tio-map"></i> {{$address['location']}}<br>
                                </a>
                            </span>
                            @else
                            <span class="d-block text-lowercase qcont">
                                {{__('messages.location').' '.__('messages.not_found')}}
                            </span>
                            @endif

                        @elseif($laundry_order->pickup_delivery_man && in_array($laundry_order->order_status, ['ready_for_delivery']))
                            <div class="w-100 text-center">
                                <div class="hs-unfold">
                                    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#myModal" data-lat='21.03' data-lng='105.85'>
                                        {{__('messages.assign_delivery_man_for_delivery_manually')}}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>

                <!-- End Body -->
                </div>
                <!-- End Card -->



                <!--Order Journey Card -->
                <div class="card mb-2">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">{{__('messages.order_journey')}}</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <dl class="row">
                            <dt class="col-sm-8">{{__('messages.order_place_time')}}:</dt>
                            <dd class="col-sm-4">{{date(config('timeformat'), strtotime($laundry_order->created_at))}}</dd>
                            <dt class="col-sm-8">{{__('messages.order_confirmed_time')}}:</dt>
                            <dd class="col-sm-4">{{$laundry_order->confirmed ? date(config('timeformat'), strtotime($laundry_order->confirmed)) : trans('messages.na')}}</dd>
                            {{-- <dt class="col-sm-8">{{__('messages.rider_reached_pickup_location')}}:</dt>
                            <dd class="col-sm-4">{{$order->accepted ? date(config('timeformat'), strtotime($order->accepted)) : trans('messages.na')}}</dd> --}}
                            <dt class="col-sm-8">{{__('messages.rider_picks_up_order')}}:</dt>
                            <dd class="col-sm-4">{{$laundry_order->picked_up ? date(config('timeformat'), strtotime($laundry_order->picked_up)) : trans('messages.na')}}</dd>
                            <dt class="col-sm-8">{{__('messages.order_processing_at')}}:</dt>
                            <dd class="col-sm-4">{{$laundry_order->processing ? date(config('timeformat'), strtotime($laundry_order->processing)) : trans('messages.na')}}</dd>
                            {{-- <dt class="col-sm-8">{{__('messages.rider_left_for_drop_off_location')}}:</dt>
                            <dd class="col-sm-4">{{$order->accepted ? date(config('timeformat'), strtotime($order->accepted)) : trans('messages.na')}}</dd> --}}
                            {{-- <dt class="col-sm-8">{{__('messages.rider_reached_drop_off_location')}}:</dt>
                            <dd class="col-sm-4">{{$order->accepted ? date(config('timeformat'), strtotime($order->accepted)) : trans('messages.na')}}</dd> --}}
                            {{-- <dt class="col-sm-8">{{__('messages.customer_receives_notification_of_rider_arrival')}}:</dt>
                            <dd class="col-sm-4">{{$order->accepted ? date(config('timeformat'), strtotime($order->accepted)) : trans('messages.na')}}</dd> --}}
                            <dt class="col-sm-8">{{__('messages.rider_delivered_and_marked_completed')}}:</dt>
                            <dd class="col-sm-4">{{$laundry_order->delivered ? date(config('timeformat'), strtotime($laundry_order->delivered)) : trans('messages.na')}}</dd>
                        </dl>
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Order Journey Card -->




                <!-- Customer Card -->
                <div class="card mb-2">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">{{__('messages.customer')}}</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    @if($laundry_order->customer)
                        <div class="card-body">

                            <a class="media align-items-center deco-none" href="{{route('admin.customer.view',[$laundry_order->customer['id']])}}">
                                <div class="avatar avatar-circle mr-3">

                                    <img class="avatar-img" style="width: 75px"
                                    onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                    src="{{asset('storage/app/public/profile/'.$laundry_order->customer->image)}}"
                                    alt="Image Description">

                                </div>
                                <div class="media-body">
                                    <span class="text-body text-hover-primary">{{$laundry_order->customer['f_name'].' '.$laundry_order->customer['l_name']}}</span><br>
                                    <span class="badge badge-ligh">{{$laundry_order->customer->laundry_orders_count}} {{__('messages.orders')}}</span>
                                </div>

                            </a>

                            <hr>

                            <div class="d-flex justify-content-between align-items-center">
                                <h5>{{__('messages.contact')}} {{__('messages.info')}}</h5>
                            </div>

                            <ul class="list-unstyled list-unstyled-py-2">
                                <li>
                                    <i class="tio-online mr-2"></i>
                                    {{$laundry_order->customer['email']}}
                                </li>
                                <li>
                                    <a class="deco-none" href="tel:{{$laundry_order->customer['phone']}}">
                                        <i class="tio-android-phone-vs mr-2"></i>
                                        {{$laundry_order->customer['phone']}}
                                    </a>
                                </li>
                            </ul>
                            @if($laundry_order->pickup_address)
                                <hr>
                                @php($address=$laundry_order->pickup_address)
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{__('messages.pickup')}} {{__('messages.info')}}</h5>
                                    {{-- @if(isset($address))
                                        <a class="link" data-toggle="modal" data-target="#picking-address-modal"
                                           href="javascript:">{{__('messages.edit')}}</a>
                                    @endif --}}
                                </div>
                                @if(isset($address))
                                    <span class="d-block">
                                        {{__('messages.name')}}: {{$address['contact_person_name']}}<br>
                                        {{__('messages.contact')}}:<a class="deco-none" href="tel:{{$address['contact_person_number']}}"> {{$address['contact_person_number']}}</a><br>
                                        @if(isset($address['address']))
                                        @if(isset($laundry_order->pickup_coordinates))
                                        <a target="_blank"
                                            href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$laundry_order->pickup_coordinates->getLat()}}+{{$laundry_order->pickup_coordinates->getLng()}}">
                                            <i class="tio-map"></i>{{$address['address']}}<br>
                                        </a>
                                        @else
                                            <i class="tio-map"></i>{{$address['address']}}<br>
                                        @endif
                                        @endif
                                    </span>
                                @endif
                            @endif
                            @if($laundry_order->destination_address)
                                <hr>
                                @php($address=$laundry_order->destination_address)
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5>{{__('messages.destination')}} {{__('messages.info')}}</h5>
                                    @if(isset($address))
                                        <a class="link" data-toggle="modal" data-target="#shipping-address-modal"
                                           href="javascript:">{{__('messages.edit')}}</a>
                                    @endif
                                </div>
                                @if(isset($address))
                                    <span class="d-block">
                                        {{__('messages.name')}}: {{$address['contact_person_name']}}<br>
                                        {{__('messages.contact')}}:<a class="deco-none" href="tel:{{$address['contact_person_number']}}"> {{$address['contact_person_number']}}</a><br>
                                        @if(isset($address['address']))
                                        @if(isset($laundry_order->destination_coordinates))
                                        <a target="_blank"
                                            href="http://maps.google.com/maps?z=12&t=m&q=loc:{{$laundry_order->destination_coordinates->getLat()}}+{{$laundry_order->destination_coordinates->getLng()}}">
                                            <i class="tio-map"></i>{{$address['address']}}<br>
                                        </a>
                                        @else
                                            <i class="tio-map"></i>{{$address['address']}}<br>
                                        @endif
                                        @endif
                                    </span>
                                @endif
                            @endif
                        </div>
                    @endif
                <!-- End Body -->
                </div>
                <!-- End Card -->

                <!--Order Journey Card -->
                <div class="card mb-2">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">{{__('messages.pickup_images')}}</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <dl class="row">
                           @if ($laundry_order->pickup_picture)
                           <div class="col-4">
                                <img class="img__aspect-1 rounded border w-100" data-toggle="modal" data-target="#imagemodal_pickup" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                    src="{{asset('storage/app/public/order-pickup').'/'.$laundry_order->pickup_picture}}">
                                </div>
                            @endif
                            <!----  Pickup Image  Modal ---->
                            <div class="modal fade" id="imagemodal_pickup" tabindex="-1" role="dialog"
                                aria-labelledby="myModalLabel" aria-hidden="true">
                                <div class="modal-dialog">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title" id="myModalLabel">
                                                {{ trans('Pickup Image') }}</h4>
                                            <button type="button" class="close" data-dismiss="modal"><span
                                                    aria-hidden="true">&times;</span><span
                                                    class="sr-only">{{ trans('messages.cancel') }}</span></button>
                                        </div>
                                        <div class="modal-body">
                                            <img src="{{asset('storage/app/public/order-pickup').'/'.$laundry_order->pickup_picture}}"
                                                class="initial--22 w-100">
                                        </div>
                                        <div class="modal-footer">
                                            <a class="btn btn-primary"
                                                href="{{ route('admin.file-manager.download', base64_encode('public/order-pickup/' . $laundry_order->pickup_picture)) }}"><i
                                                    class="tio-download"></i> {{ trans('messages.download') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!---- End Pickup Image  Modal ---->
                        </dl>
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Order Journey Card -->
                <!--Order Journey Card -->
                <div class="card mb-2">
                    <!-- Header -->
                    <div class="card-header">
                        <h4 class="card-header-title">{{__('messages.delivery_images')}}</h4>
                    </div>
                    <!-- End Header -->

                    <!-- Body -->
                    <div class="card-body">
                        <dl class="row">
                            @if($laundry_order->delivery_picture)
                                <div class="col-4">
                                    <img class="img__aspect-1 rounded border w-100" data-toggle="modal" data-target="#imagemodal_delivery" onerror="this.src='{{ asset('public/assets/admin/img/160x160/img2.jpg') }}"
                                    src="{{asset('storage/app/public/order-delivery').'/'.$laundry_order->delivery_picture}}">
                                </div>
                            @endif

                            <!---- Delivery Image  Modal ---->
                            <div class="modal fade" id="imagemodal_delivery" tabindex="-1" role="dialog"
                            aria-labelledby="myModalLabel" aria-hidden="true">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title" id="myModalLabel">
                                            {{ trans('Delivery Image') }}</h4>
                                        <button type="button" class="close" data-dismiss="modal"><span
                                                aria-hidden="true">&times;</span><span
                                                class="sr-only">{{ trans('messages.cancel') }}</span></button>
                                    </div>
                                    <div class="modal-body">
                                        <img src="{{asset('storage/app/public/order-delivery').'/'.$laundry_order->delivery_picture}}"
                                            class="initial--22 w-100">
                                    </div>
                                    <div class="modal-footer">
                                        <a class="btn btn-primary"
                                            href="{{ route('admin.file-manager.download', base64_encode('public/order-delivery/' . $laundry_order->delivery_picture)) }}"><i
                                                class="tio-download"></i> {{ trans('messages.download') }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                            </div>
                            <!---- Delivery Image  Modal ---->
                        </dl>
                    </div>
                    <!-- End Body -->
                </div>
                <!-- End Order Journey Card -->

            </div>
        </div>
        <!-- End Row -->
    </div>

    <!-- Modal -->
    <div class="modal fade bd-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-sm" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title h4" id="mySmallModalLabel">{{__('messages.reference')}} {{__('messages.code')}} {{__('messages.add')}}</h5>
                    <button type="button" class="btn btn-xs btn-icon btn-ghost-secondary" data-dismiss="modal"
                            aria-label="Close">
                        <i class="tio-clear tio-lg"></i>
                    </button>
                </div>

                <form action="{{route('admin.laundry.order.add-payment-ref-code',[$laundry_order['id']])}}" method="post">
                    @csrf
                    <div class="modal-body">
                        <!-- Input Group -->
                        <div class="form-group">
                            <input type="text" name="transaction_reference" class="form-control"
                                   placeholder="EX : Code123" required>
                        </div>
                        <!-- End Input Group -->
                        <button class="btn btn-primary">{{__('messages.submit')}}</button>
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!-- Modal -->
    <div id="shipping-address-modal" class="modal fade" tabindex="-1" role="dialog"
         aria-labelledby="exampleModalTopCoverTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Header -->
                <div class="modal-top-cover bg-dark text-center">
                    <figure class="position-absolute right-0 bottom-0 left-0" style="margin-bottom: -1px;">
                        <svg preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg" x="0px" y="0px"
                             viewBox="0 0 1920 100.1">
                            <path fill="#fff" d="M0,0c0,0,934.4,93.4,1920,0v100.1H0L0,0z"/>
                        </svg>
                    </figure>

                    <div class="modal-close">
                        <button type="button" class="btn btn-icon btn-sm btn-ghost-light" data-dismiss="modal"
                                aria-label="Close">
                            <svg width="16" height="16" viewBox="0 0 18 18" xmlns="http://www.w3.org/2000/svg">
                                <path fill="currentColor"
                                      d="M11.5,9.5l5-5c0.2-0.2,0.2-0.6-0.1-0.9l-1-1c-0.3-0.3-0.7-0.3-0.9-0.1l-5,5l-5-5C4.3,2.3,3.9,2.4,3.6,2.6l-1,1 C2.4,3.9,2.3,4.3,2.5,4.5l5,5l-5,5c-0.2,0.2-0.2,0.6,0.1,0.9l1,1c0.3,0.3,0.7,0.3,0.9,0.1l5-5l5,5c0.2,0.2,0.6,0.2,0.9-0.1l1-1 c0.3-0.3,0.3-0.7,0.1-0.9L11.5,9.5z"/>
                            </svg>
                        </button>
                    </div>
                </div>
                <!-- End Header -->

                <div class="modal-top-cover-icon">
                    <span class="icon icon-lg icon-light icon-circle icon-centered shadow-soft">
                      <i class="tio-location-search"></i>
                    </span>
                </div>
                @if(isset($address))
                    <form action="{{route('admin.laundry.order.update-shipping',[$laundry_order['id']])}}"
                          method="post">
                        @csrf
                        <div class="modal-body">
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{__('messages.contact')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_number"
                                           value="{{$address['contact_person_number']}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{__('messages.name')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="contact_person_name"
                                           value="{{$address['contact_person_name']}}" required>
                                </div>
                            </div>
                            <div class="row mb-3">
                                <label for="requiredLabel" class="col-md-2 col-form-label input-label text-md-right">
                                    {{__('messages.address')}}
                                </label>
                                <div class="col-md-10 js-form-message">
                                    <input type="text" class="form-control" name="address"
                                           value="{{$address['address']}}"
                                           >
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-white" data-dismiss="modal">{{__('messages.close')}}</button>
                            <button type="submit" class="btn btn-primary">{{__('messages.save')}} {{__('messages.changes')}}</button>
                        </div>
                    </form>
                @endif
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!--Dm assign Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{__('messages.assign')}} {{__('messages.deliveryman')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 my-2">
                            <ul class="list-group overflow-auto" style="max-height:400px;">
                                @foreach ($deliveryMen as $dm)
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-2">
                                        <div class="dm_list d-flex gap-2" role='button' data-id="{{$dm['id']}}">
                                            <img class="avatar avatar-sm avatar-circle mr-1" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}" alt="{{$dm['name']}}">
                                            <div class="d-flex flex-column">
                                                <span class="text-black font-weight-bold">{{$dm['name']}}</span>
                                                <span class="dm_list">
                                                    <div class="text-secondary">
                                                        {{ trans('messages.vehicle') }}: {{$dm['vehicle']}}
                                                    </div>
                                                </span>
                                            </div>
                                        </div>

                                        <a class="btn btn-primary btn-xs float-right" onclick="addDeliveryMan({{$dm['id']}})">{{__('messages.assign')}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-7 modal_body_map">
                            <div class="location-map" id="dmassign-map">
                                <div style="width: 600px; height: 400px;" id="map_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <!--Show locations on map Modal -->
    <div class="modal fade" id="locationModal" tabindex="-1" role="dialog" aria-labelledby="locationModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="locationModalLabel">{{__('messages.location')}} {{__('messages.data')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 modal_body_map">
                            <div class="location-map" id="location-map">
                                <div style="width: 100%; height: 400px;" id="location_map_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->

    <div class="modal fade" id="quick-view" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content" id="quick-view-modal">

            </div>
        </div>
    </div>

  <!-- End Modal -->

@endsection

@push('script_2')

    <script>
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var keyword= $('#datatableSearch').val();
            var nurl = new URL('{!!url()->full()!!}');
            nurl.searchParams.set('keyword', keyword);
            location.href = nurl;
        });
    </script>

    <script src="https://maps.googleapis.com/maps/api/js?key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&v=3.45.8"></script>
    <script>
        var order_ids = [{{$laundry_order['id']}}];

        function addDeliveryMan(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{route('admin.laundry.delivery-man.add-delivery-man')}}",
                data: {
                    "_token": "{{ csrf_token() }}",
                    'delivery_man_id':id,
                    'order_ids': order_ids
                },
                success: function (data) {
                    location.reload();
                    console.log(data)
                    toastr.success('Successfully added', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                error: function (response) {
                    console.log(response);
                    toastr.error(response.responseJSON.message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function last_location_view() {
            toastr.warning('Only available when order is out for delivery!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>

    <script>
        var deliveryMan = <?php echo(json_encode($deliveryMen)); ?>;

        var map = null;
        var myLatlng = new google.maps.LatLng(49.4558236992736, 11.084644538780994);
        var dmbounds = new google.maps.LatLngBounds (null);
        var locationbounds = new google.maps.LatLngBounds (null);
        var dmMarkers = [];
        dmbounds.extend(myLatlng);
        locationbounds.extend(myLatlng);
        var myOptions = {
            center: myLatlng,
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP,

            panControl: true,
            mapTypeControl: false,
            panControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            scaleControl: false,
            streetViewControl: false,
            streetViewControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            }
        };
        function initializeGMap() {

            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

            var infowindow = new google.maps.InfoWindow();

            map.fitBounds(dmbounds);
            for (var i = 0; i < deliveryMan.length; i++) {
                if(deliveryMan[i].lat)
                {
                    // var contentString = "<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{asset('storage/app/public/delivery-man')}}/"+deliveryMan[i].image+"'></div><div style='float:right; padding: 10px;'><b>"+deliveryMan[i].name+"</b><br/> "+deliveryMan[i].location+"</div>";
                    var point = new google.maps.LatLng(deliveryMan[i].lat, deliveryMan[i].lng);
                    console.log(deliveryMan[i].lat, deliveryMan[i].lng);
                    dmbounds.extend(point);
                    map.fitBounds(dmbounds);
                    var marker = new google.maps.Marker({
                        position: point,
                        map: map,
                        title: deliveryMan[i].location,
                        icon: "{{asset('public/assets/admin/img/delivery_boy_map.png')}}"
                    });
                    dmMarkers[deliveryMan[i].id]=marker;
                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                            infowindow.setContent("<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{asset('storage/app/public/delivery-man')}}/"+deliveryMan[i].image+"'></div><div style='float:right; padding: 10px;'><b>"+deliveryMan[i].name+"</b><br/> "+deliveryMan[i].location+"</div>");
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                }

            };
        }
        $(document).ready(function() {

            // Re-init map before show modal
            $('#myModal').on('shown.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                $("#dmassign-map").css("width", "100%");
                $("#map_canvas").css("width", "100%");
            });

            // Trigger map resize event after modal shown
            $('#myModal').on('shown.bs.modal', function() {
                initializeGMap();
                google.maps.event.trigger(map, "resize");
                map.setCenter(myLatlng);
            });


            function initializegLocationMap() {
                map = new google.maps.Map(document.getElementById("location_map_canvas"), myOptions);

                var infowindow = new google.maps.InfoWindow();

                @if($laundry_order->customer && isset($address))
                var marker = new google.maps.Marker({
                    position: new google.maps.LatLng({{$laundry_order->destination_coordinates->getLat()}}, {{$laundry_order->destination_coordinates->getLng()}}),
                    map: map,
                    title: "{{$laundry_order->customer->f_name}} {{$laundry_order->customer->l_name}}",
                    icon: "{{asset('public/assets/admin/img/customer_location.png')}}"
                });

                google.maps.event.addListener(marker, 'click', (function(marker) {
                    return function() {
                        infowindow.setContent("<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{asset('storage/app/public/profile/'.$laundry_order->customer->image)}}'></div><div style='float:right; padding: 10px;'><b>{{$laundry_order->customer->f_name}} {{$laundry_order->customer->l_name}}</b><br/>{{$address['address']}}</div>");
                        infowindow.open(map, marker);
                    }
                })(marker));
                locationbounds.extend(marker.getPosition());
                @endif
                @if($laundry_order->delivery_man && $laundry_order->dm_last_location)
                var dmmarker = new google.maps.Marker({
                    position: new google.maps.LatLng({{$laundry_order->dm_last_location['latitude']}}, {{$laundry_order->dm_last_location['longitude']}}),
                    map: map,
                    title: "{{$laundry_order->delivery_man->f_name}}  {{$laundry_order->delivery_man->l_name}}",
                    icon: "{{asset('public/assets/admin/img/delivery_boy_map.png')}}"
                });

                google.maps.event.addListener(dmmarker, 'click', (function(dmmarker) {
                    return function() {
                        infowindow.setContent("<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{asset('storage/app/public/delivery-man/'.$laundry_order->delivery_man->image)}}'></div><div style='float:right; padding: 10px;'><b>{{$laundry_order->delivery_man->f_name}}  {{$laundry_order->delivery_man->l_name}}</b><br/> {{$laundry_order->dm_last_location['location']}}</div>");
                        infowindow.open(map, dmmarker);
                    }
                })(dmmarker));
                locationbounds.extend(dmmarker.getPosition());
                @endif

                @if ($laundry_order->destination_coordinates)
                var Retaurantmarker = new google.maps.Marker({
                    position: new google.maps.LatLng({{$laundry_order->destination_coordinates->getLat()}}, {{$laundry_order->destination_coordinates->getLng()}}),
                    map: map,
                    title: "{{Str::limit($laundry_order?->destination_address['contact_person_name']??null, 15,'...')}}",
                    icon: "{{asset('public/assets/admin/img/restaurant_map.png')}}"
                });

                google.maps.event.addListener(Retaurantmarker, 'click', (function(Retaurantmarker) {
                    return function() {
                        infowindow.setContent("<div style='float:left'><img style='max-height:40px;wide:auto;' src='https://cdn-icons-png.flaticon.com/512/8170/8170709.png'></div><div style='float:right; padding: 10px;'><b>{{Str::limit($laundry_order?->destination_address['contact_person_name']??null, 15, '...')}}</b><br/> {{$laundry_order?->destination_address['address']??null}}</div>");
                        infowindow.open(map, Retaurantmarker);
                    }
                })(Retaurantmarker));
                locationbounds.extend(Retaurantmarker.getPosition());
                @endif

                google.maps.event.addListenerOnce(map, 'idle', function() {
                    map.fitBounds(locationbounds);
                });
            }

            // Re-init map before show modal
            $('#locationModal').on('shown.bs.modal', function(event) {
                initializegLocationMap();
            });

            $('.dm_list').on('click', function() {
                var id = $(this).data('id');
                console.log(id);
                map.panTo(dmMarkers[id].getPosition());
                map.setZoom(13);
                dmMarkers[id].setAnimation(google.maps.Animation.BOUNCE);
                window.setTimeout(() => {
                    dmMarkers[id].setAnimation(null);
                }, 3);
            });
        })

    </script>
@endpush
