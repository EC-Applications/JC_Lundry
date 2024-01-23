@extends('layouts.admin.app')

@section('title','Update Banner')

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
                    <h1 class="page-header-title"><i class="tio-edit"></i>{{__('messages.update')}} {{__('messages.vehicle_type')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.laundry.vehicle-type.update', [$type->id])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}}</label>
                                        <input type="text" value="{{ $type->name }}" name="name" class="form-control" placeholder="{{__('messages.new_vehicle_type')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-block btn-primary" style="margin-top: 29px">{{__('messages.update')}}</button>
                                </div>
                            </div>
                           
                        </form>
                    </div>
                </div>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
<script>
    function getRequest(route, id) {
        $.get({
            url: route,
            dataType: 'json',
            success: function (data) {
                $('#' + id).empty().append(data.options);
            },
        });
    }
    function readURL(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#viewer').attr('src', e.target.result);
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#customFileEg1").change(function () {
        readURL(this);
    });
</script>
<script>
    $(document).on('ready', function () {

        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });   
    });
    </script>
@endpush
