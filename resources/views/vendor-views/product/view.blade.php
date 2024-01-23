@extends('layouts.vendor.app')

@section('title','Food Preview')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-6">
                    <h1 class="page-header-title text-break">{{$product['name']}}</h1>
                </div>
                <div class="col-6">
                    <a href="{{route('vendor.food.edit',[$product['id']])}}" class="btn btn-primary float-right">
                        <i class="tio-edit"></i> {{__('messages.edit')}}
                    </a>
                </div>
            </div>
            @auth('vendor')
            <div class="js-nav-scroller hs-nav-scroller-horizontal mb-2">
                <!-- Nav -->
                <ul class="nav nav-tabs page-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link {{$tab=='details'?'active':''}}" href="{{route('vendor.food.view', [$product->id])}}">{{__('messages.details')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{$tab=='log'?'active':''}}" href="{{route('vendor.food.view', [$product->id,'log'])}}"  aria-disabled="true">{{__('messages.log')}}</a>
                    </li>
                </ul>
                <!-- End Nav -->
            </div>
            @endauth
        </div>
        <!-- End Page Header -->
        @include("vendor-views.product.partials._{$tab}")
    </div>
@endsection

@push('script_2')

@endpush
