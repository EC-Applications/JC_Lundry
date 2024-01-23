@extends('layouts.admin.app')

@section('title',__('messages.Update category'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i>{{__('messages.update_module')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.module.update',[$module['id']])}}" method="post" enctype="multipart/form-data">
                    @method('PUT')
                    @csrf
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.module')}} {{__('messages.name')}}</label>
                        <input type="text" name="module_name" class="form-control" value="{{$module->module_name}}" required maxlength="191">
                    </div>
                    <div class="form-group">
                        <label class="input-label" for="module_type">{{__('messages.module_type')}}</label>
                        <select name="module_type" id="module_type" class="form-control text-capitalize" disabled>
                            @foreach (config('module.module_type') as $key)
                                <option value="{{$key}}" {{$key==$module->module_type?'selected':''}}>{{$key}}</option>
                            @endforeach
                        </select>
                        <div class="card mt-1" id="module_des_card">
                            <div class="card-body" id="module_description">{{config('module.'.$module->module_type)['description']}}</div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label>{{__('messages.image')}}</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                           accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <center>
                                <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer"
                                     src="{{asset('storage/app/public/module/'.$module['thumbnail'])}}" alt=""/>
                            </center>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">{{__('messages.update')}}</button>
                </form>
            </div>
            <!-- End Table -->
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        function modulChange(id)
        {
            $.get({
                url: "{{url('/')}}/admin/module/type/?module_type="+id,
                dataType: 'json',
                success: function (data) {
                    $('#module_des_card').show();
                    $('#module_description').html(data.data.description);
                    console.log(data.data.description);
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
@endpush
