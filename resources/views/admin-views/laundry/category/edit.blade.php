@extends('layouts.admin.app')

@section('title',__('messages.update_laundry_category'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i>{{__('messages.update_laundry_category')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.laundry.category.update',[$laundry_category['id']])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="card p-4">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}} </label>
                            <input type="text" name="name" class="form-control" value="{{$laundry_category['name']}}" required>
                        </div>
                        <div class="form-group pt-4">
                            <label class="input-label" for="exampleFormControlInput1">{{__('messages.short')}} {{__('messages.description')}}</label>
                            <textarea type="text" name="description" class="form-control ckeditor">{!! $laundry_category['description'] !!}</textarea>
                        </div>
                    </div>
                    @if($laundry_category->position == 0)
                    <div class="form-group">
                        <label class="input-label">{{__('messages.module')}}</label>
                        <select name="module_id" required class="form-control js-select2-custom"  data-placeholder="{{__('messages.select')}} {{__('messages.module')}}">
                                <option value="" selected disabled>{{__('messages.select')}} {{__('messages.module')}}</option>
                            @foreach(\App\Models\Module::laundry()->get() as $module)
                                <option value="{{$module->id}}" {{$laundry_category->module_id==$module->id?'selected':''}}>{{$module->module_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
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
                                     src="{{asset('storage/app/public/laundry_category')}}/{{$laundry_category['image']}}" alt=""/>
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
