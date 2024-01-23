@extends('layouts.admin.app')

@section('title','Add new shift')

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} {{__('messages.shift')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{ route('admin.shift.store') }}" method="post" id="shift_form"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="title">{{__('messages.title')}}</label>
                                <input type="text" name="title" class="form-control" placeholder="{{__('messages.morning_shift')}}" required maxlength="191" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="input-label" for="title">{{__('messages.zone')}}</label>
                                <select name="zone_id" id="zone" class="form-control js-select2-custom">
                                    <option disabled selected>---{{__('messages.select')}}---</option>
                                    @php($zones=\App\Models\Zone::all())
                                    @foreach($zones as $zone)
                                        @if(isset(auth('admin')->user()->zone_id))
                                            @if(auth('admin')->user()->zone_id == $zone->id)
                                                <option value="{{$zone->id}}" selected>{{$zone->name}}</option>
                                            @endif
                                        @else
                                        <option value="{{$zone['id']}}">{{$zone['name']}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 col-6">
                            <div class="form-group">
                                <label class="input-label" for="title">{{__('messages.start')}} {{__('messages.time')}}</label>
                                <input type="time" id="start_time" class="form-control" name="start_time"
                                required>
                            </div>
                        </div>
                        <div class="col-md-6 col-6">
                            <div class="form-group">
                                <label class="input-label" for="title">{{__('messages.end')}} {{__('messages.time')}}</label>
                                <input type="time" id="end_time" class="form-control" name="end_time"
                                required>
                            </div>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                </form>
            </div>
        </div>
    </div>

@endsection
