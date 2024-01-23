@extends('layouts.admin.app')

@section('title','Update Laundry Delivery Type')

@push('css_or_js')
<style>
    .select2 .select2-container .select2-container--default .select2-container--above .select2-container--focus{
        width:100% !important;
    }
</style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i>{{__('messages.update')}} {{__('messages.laundry_delivery_type')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.laundry.delivery-type.update', [$type->id])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.title')}}</label>
                                        <input type="text" value="{{$type->title}}" name="title" class="form-control" placeholder="{{__('messages.new_delivery_type_name')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.duration')}}</label>
                                        <input type="text" value="{{$type->duration}}" name="duration" class="form-control" placeholder="{{__('messages.duration_in_day')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.charge')}}</label>
                                        <input type="text" value="{{$type->charge}}" name="charge" class="form-control" placeholder="{{__('messages.charge')}}" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{__('messages.update')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')

@endpush
