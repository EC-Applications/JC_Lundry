@extends('layouts.admin.app')

@section('title', trans('messages.deliveryman_preview'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard') }}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{ __('messages.deliveryman') }} {{ __('messages.view') }}
                </li>
            </ol>
        </nav>
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-6">
                    <h1>{{$dm->f_name.' '.$dm->l_name}}</h1>
                </div>
                <div class="col-6">
                    <a href="{{ url()->previous() }}" class="btn btn-primary float-right">
                        <i class="tio-back-ui"></i> {{ __('messages.back') }}
                    </a>
                </div>

                <div class="js-nav-scroller hs-nav-scroller-horizontal">
                    <!-- Nav -->
                    <ul class="nav nav-tabs page-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link active"
                                href="{{ route('admin.laundry.delivery-man.preview', ['id' => $dm->id, 'tab' => 'profile']) }}"
                                aria-disabled="true">{{ __('messages.profile') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.laundry.delivery-man.preview', ['id' => $dm->id, 'tab' => 'order']) }}"
                                aria-disabled="true">{{ __('messages.orders') }}</a>
                        </li>
                    </ul>
                    <!-- End Nav -->
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="row">
            <div class="col-lg-12">
                <div class="card mb-3 mb-lg-5 mt-2">
                    <div class="card-header">
                        {{trans('messages.rider_details')}}
                    </div>
                    <!-- Body -->
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-6 col-md-6 col-lg-3">
                                <img style="width: 200px;border: 1px solid; border-radius: 10px;" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                                src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}" alt="{{$dm['f_name']}} {{$dm['l_name']}}"/> 
                            </div>
                            <div class="col-sm-6 col-md-16 col-lg-9">
                                <small class="card-subtitle border-bottom">{{trans('messages.personal_details')}}</small>
                                <dl class="row">
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.name')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8">{{$dm->f_name.' '.$dm->l_name}}</dd>
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.phone')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8"><a href="tel:{{$dm->phone}}">{{$dm->phone}}</a></dd>
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.email')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8"><a href="mailto:{{$dm->email}}">{{$dm->email}}</a></dd>
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.zone')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8">{{$dm->laundry_zone?$dm->laundry_zone->name:trans('messages.not_found')}}</dd>
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.joining_date')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8">{{$dm->created_at ? date("d M Y", strtotime($dm->created_at)) : trans('messages.na')}}</dd>
                                </dl>
                            </div>
                        </div>
                    </div>
                    <!-- End Body -->
                </div>
            </div>
        </div>
        <!-- End Card -->
    </div>
@endsection
