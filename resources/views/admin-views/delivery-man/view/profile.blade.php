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
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'info']) }}"
                                aria-disabled="true">{{ __('messages.info') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'profile']) }}"
                                aria-disabled="true">{{ __('messages.profile') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'transaction']) }}"
                                aria-disabled="true">{{ __('messages.transaction') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'order']) }}"
                                aria-disabled="true">{{ __('messages.orders') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'suspension'])}}"  aria-disabled="true">{{__('messages.suspension_logs')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'timelog'])}}"  aria-disabled="true">{{__('messages.time_logs')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'online_percentage'])}}"  aria-disabled="true">{{__('messages.online_percentage')}}</a>
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
                            <div class="col-md-6">
                                <small class="card-subtitle border-bottom">{{trans('messages.personal_details')}}</small>
                                <dl class="row">
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.name')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8">{{$dm->f_name.' '.$dm->l_name}}</dd>
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.phone')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8"><a href="tel:{{$dm->phone}}">{{$dm->phone}}</a></dd>
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.email')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8"><a href="mailto:{{$dm->email}}">{{$dm->email}}</a></dd>
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.zone')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8">{{$dm->zone?$dm->zone->name:trans('messages.not_found')}}</dd>
                                    <dt class="col-lg-2 col-md-3 col-4">{{__('messages.joining_date')}}:</dt>
                                    <dd class="col-lg-10 col-md-9 col-8">{{$dm->created_at ? date("d M Y", strtotime($dm->created_at)) : trans('messages.na')}}</dd>
                                </dl>
                            </div>
                            <div class="col-md-6">
                                @if (count($dm->documents))
                                    <small class="card-subtitle border-bottom">{{trans('messages.documents')}}</small>
                                    @foreach ($dm->documents as $key=>$doc)
                                    {{-- <h3 class="qcont px-3 pt-4">{{ $doc['type'] }}</h3> --}}
                                    <div class="row">
                                        <dt class="col-lg-2 col-md-3 col-4">{{$doc['type']}}:</dt>
                                        <dd class="col-lg-10 col-md-9 col-8">{{$doc['data']}}</dd>
                                        @php($files = $doc->files)
                                        @if ($files && count($files))
                                            <dt class="col-lg-2 col-md-3 col-4">{{$doc['type'].' '.trans('messages.files')}}:</dt>
                                            <dd class="col-lg-10 col-md-9 col-8">
                                                @foreach ($files as $k=>$file)
                                                    @if ($doc['document_type'] == 'image')
                                                        <a class="badge badge-info" data-toggle="modal" data-target="#imageModal-{{$key}}-{{$k}}" title="{{ $file }}">{{ Str::limit($file, 15) }} </a>
                                                        <div class="modal fade" id="imageModal-{{$key}}-{{$k}}" tabindex="-1"
                                                            role="dialog" aria-labelledby="imageModal" aria-hidden="true">
                                                            <div class="modal-dialog">
                                                                <div class="modal-content">
                                                                    <div class="modal-header">
                                                                        <h4 class="modal-title">{{$file}}</h4>
                                                                        <button type="button" class="close" data-dismiss="modal"><span
                                                                                aria-hidden="true">&times;</span><span
                                                                                class="sr-only">{{trans('messages.Close')}}</span></button>
                                                                    </div>
                                                                    <div class="modal-body">
                                                                        <img src="{{asset("storage/app/public/delivery-man/{$file}")}}" style="height: auto; width:100%">
                                                                    </div>
                                                                    <div class="modal-footer">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @elseif ($doc['document_type'] == 'pdf')
                                                        <a class="badge badge-primary" href="{{ asset("storage/app/public/delivery-man/{$file}") }}" target="_blank" title="{{ $file }}">{{ Str::limit($file, 15) }}</a>                                            
                                                    @endif
                                                @endforeach
                                            </dd>                                        
                                        @endif
                                    </div>
                                    @endforeach                                    
                                @endif

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
