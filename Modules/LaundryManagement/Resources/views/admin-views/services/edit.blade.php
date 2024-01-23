@extends('layouts.admin.app')

@section('title',__('messages.update_service'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i>{{__('messages.update_service')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="card">
            <div class="card-body">
                <form action="{{route('admin.laundry.service.update',[$service['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} </label>
                        <input type="text" name="name" class="form-control" value="{{$service['name']}}" required>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{__('messages.icon')}}</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="icon" id="customFileEg1" class="custom-file-input"
                                           accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <center>
                                <img style="width: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                     src="{{asset('storage/app/public/services')}}/{{$service['icon']}}" alt=""/>
                            </center>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-sm btn-primary">{{__('messages.update')}}</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
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
@endpush
