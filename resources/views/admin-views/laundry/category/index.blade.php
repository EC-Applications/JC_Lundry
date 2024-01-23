@extends('layouts.admin.app')

@section('title',__('messages.laundry_category'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title">{{__('messages.laundry_category')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="card">
            <div class="card-header"><h5>{{__('messages.add_new_laundry_category')}}</h5></div>
            <div class="card-body">
                <form action="{{route('admin.laundry.category.store')}}" method="post" enctype="multipart/form-data">
                    @csrf          
                    <div class="card p-4">
                        <div class="form-group">
                            <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}}</label>
                            <input type="text" name="name" class="form-control" placeholder="{{__('messages.new_item')}}" required>
                        </div>
                        <div class="form-group pt-4">
                            <label class="input-label" for="exampleFormControlInput1">{{__('messages.short')}} {{__('messages.description')}}</label>
                            <textarea type="text" name="description" class="form-control ckeditor"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="input-label">{{__('messages.module')}}</label>
                        <select name="module_id" required class="form-control js-select2-custom"  data-placeholder="{{__('messages.select')}} {{__('messages.module')}}">
                                <option value="" selected disabled>{{__('messages.select')}} {{__('messages.module')}}</option>
                            @foreach(\App\Models\Module::laundry()->get() as $module)
                                <option value="{{$module->id}}" >{{$module->module_name}}</option>
                            @endforeach
                        </select>
                    </div>
                    <input name="position" value="0" style="display: none">
                    <div class="form-group">
                        <label>{{__('messages.image')}}</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1)</small>
                        <div class="custom-file">
                            <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                            <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom:0%;">
                        <center>
                            <img style="width: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                src="{{asset('public/assets/admin/img/900x400/img1.jpg')}}"
                                alt="image"/>
                        </center>
                    </div>
                    <div class="form-group pt-2">
                        <button type="submit" class="btn btn-primary">{{__('messages.Add')}}</button>
                    </div>
                    
                </form>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header pb-2">
                <h5 class="col">{{__('messages.laundry_category')}} {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$laundry_categories->total()}}</span></h5>
                {{-- <div class="col">
                    <select name="module_id" class="form-control js-select2-custom" onchange="set_filter('{{url()->full()}}',this.value,'module_id')" title="{{__('messages.select')}} {{__('messages.modules')}}">
                        <option value="" {{!request('module_id') ? 'selected':''}}>{{__('messages.all')}} {{__('messages.modules')}}</option>
                        @foreach (\App\Models\Module::notLaundry()->get() as $module)
                            <option
                                value="{{$module->id}}" {{request('module_id') == $module->id?'selected':''}}>
                                {{$module['module_name']}}
                            </option>
                        @endforeach
                    </select>
                </div> --}}

                {{--<form id="dataSearch" class="col">
                    @csrf
                    <!-- Search -->
                    <div class="input-group input-group-merge input-group-flush">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                <i class="tio-search"></i>
                            </div>
                        </div>
                        <input type="search" name="search" class="form-control" placeholder="{{__('messages.search_categories')}}" aria-label="{{__('messages.search_categories')}}">
                        <button type="submit" class="btn btn-light">{{__('messages.search')}}</button>
                    </div>
                    <!-- End Search -->
                </form>--}}
            </div>
            <div class="card-body">
                <div class="table-responsive datatable-custom">
                    <table id="columnSearchDatatable"
                        class="table table-borderless table-thead-bordered table-align-middle" style="width:100%;"
                        data-hs-datatables-options='{
                            "isResponsive": false,
                            "isShowPaging": false,
                            "paging":false,
                        }'>
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 5%">{{__('messages.#')}}</th>
                                <th style="width: 10%">{{__('messages.id')}}</th>
                                <th style="width: 15%">{{__('messages.name')}}</th>
                                <th style="width: 15%">{{__('messages.module')}}</th>
                                <th style="width: 10%">{{__('messages.status')}}</th>
                                <th style="width: 20%">{{__('messages.orders')}} {{__('messages.count')}}</th>
                                <th style="width: 25%">{{__('messages.action')}}</th>
                            </tr>
                        </thead>

                        <tbody id="table-div">
                        @foreach($laundry_categories as $key=>$category)
                            <tr>
                                <td>{{$key+$laundry_categories->firstItem()}}</td>
                                <td>{{$category->id}}</td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($category['name'], 20,'...')}}
                                    </span>
                                </td>
                                <td>
                                    <span class="d-block font-size-sm text-body">
                                        {{Str::limit($category->module->module_name, 15,'...')}}
                                    </span>
                                </td>
                                <td>
                                    <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$category->id}}">
                                    <input type="checkbox" onclick="location.href='{{route('admin.laundry.category.status',[$category['id'],$category->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$category->id}}" {{$category->status?'checked':''}}>
                                        <span class="toggle-switch-label">
                                            <span class="toggle-switch-indicator"></span>
                                        </span>
                                    </label>
                                </td>
                                <td>
                                    {{$category->orders_count}}
                                </td>
                                <td>
                                    <a class="btn btn-sm btn-white"
                                        href="{{route('admin.laundry.category.edit',[$category['id']])}}" title="{{__('messages.edit')}} {{__('messages.category')}}"><i class="tio-edit"></i>
                                    </a>
                                    <a class="btn btn-sm btn-white" href="javascript:"
                                    onclick="form_alert('category-{{$category['id']}}','Want to delete this category')" title="{{__('messages.delete')}} {{__('messages.category')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.parcel.category.destroy',[$category['id']])}}" method="post" id="category-{{$category['id']}}">
                                        @csrf @method('delete')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer page-area">
                <!-- Pagination -->
                <div class="row justify-content-center justify-content-sm-between align-items-sm-center"> 
                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            <!-- Pagination -->
                            {!! $laundry_categories->links() !!}
                        </div>
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
        </div>

    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            

            $('#dataSearch').on('submit', function (e) {
                e.preventDefault();
                var formData = new FormData(this);
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.post({
                    url: '{{route('admin.category.search')}}',
                    data: formData,
                    cache: false,
                    contentType: false,
                    processData: false,
                    beforeSend: function () {
                        $('#loading').show();
                    },
                    success: function (data) {
                        $('#table-div').html(data.view);
                        $('#itemCount').html(data.count);
                        $('.page-area').hide();
                    },
                    complete: function () {
                        $('#loading').hide();
                    },
                });
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>

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
