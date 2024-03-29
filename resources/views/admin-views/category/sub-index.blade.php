@extends('layouts.admin.app')

@section('title','Add new sub menu')

@push('css_or_js')
    <meta name="csrf-token" content="{{ csrf_token() }}">
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i
                            class="tio-add-circle-outlined"></i> {{__('messages.add')}} {{__('messages.new')}} {{__('messages.sub')}} {{__('messages.menu')}}
                    </h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h5>{{isset($category)?__('messages.update'):__('messages.add').' '.__('messages.new')}} {{__('messages.sub')}} {{__('messages.menu')}}</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{isset($category)?route('admin.category.update',[$category['id']]):route('admin.category.store')}}" method="post">
                        @csrf
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlSelect1">{{__('messages.main')}} {{__('messages.category')}}
                                    <span class="input-label-secondary">*</span></label>
                                <select id="exampleFormControlSelect1" name="parent_id" class="form-control js-select2-custom" required>
                                    @foreach(\App\Models\Category::where(['position'=>0])->get() as $cat)
                                        <option value="{{$cat['id']}}" {{isset($category)?($category['parent_id']==$cat['id']?'selected':''):''}} >{{$cat['name']}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="input-label"
                                    for="exampleFormControlInput1">{{__('messages.name')}}</label>
                                <input type="text" name="name" value="{{isset($category)?$category['name']:''}}"  class="form-control" placeholder="{{__('messages.sub')}} {{__('messages.menu')}}"
                                    required>
                            </div>
                            <input name="position" value="1" style="display: none">
                            <button type="submit" class="btn btn-primary">{{isset($category)?__('messages.update'):__('messages.add')}}</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header pb-0">
                        <h5>{{__('messages.sub')}} {{__('messages.menu')}} {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$categories->total()}}</span></h5>
                        <form id="dataSearch">
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input type="hidden" name="sub_category" value="1">
                                <input id="datatableSearch" name="search" type="search" class="form-control" placeholder="Sub Menu Search" aria-label="{{__('messages.search_sub_categories')}}">
                                <button type="submit" class="btn btn-light">{{__('messages.search')}}</button>
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive datatable-custom">
                            <table id="columnSearchDatatable"
                                class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                                style="width: 100%;"
                                data-hs-datatables-options='{
                                    "search": "#datatableSearch",
                                    "entries": "#datatableEntries",
                                    "isResponsive": false,
                                    "isShowPaging": false,
                                    "paging":false,
                                }'>
                                <thead class="thead-light">
                                    <tr>
                                        <th>{{__('messages.#')}}</th>
                                        <th>{{__('messages.id')}}</th>
                                        <th>{{__('messages.main')}} {{__('messages.category')}}</th>
                                        <th>{{__('messages.sub')}} {{__('messages.menu')}}</th>
                                        <th >{{__('messages.status')}}</th>
                                        <th >{{__('messages.priority')}}</th>
                                        <th >{{__('messages.action')}}</th>
                                    </tr>
                                </thead>

                                <tbody id="table-div">
                                @foreach($categories as $key=>$category)
                                    <tr>
                                        <td>{{$key+$categories->firstItem()}}</td>
                                        <td>{{$category->id}}</td>
                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                {{Str::limit($category->parent['name'],20,'...')}}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="d-block font-size-sm text-body">
                                                {{Str::limit($category->name,20,'...')}}
                                            </span>
                                        </td>
                                        <td>
                                            <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$category->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.category.status',[$category['id'],$category->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$category->id}}" {{$category->status?'checked':''}}>
                                                <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                </span>
                                            </label>
                                        </td>
                                        <td style="width:max-content;">
                                            <td>
                                                <form action="{{route('admin.category.priority',$category->id)}}">
                                                    <input name="priority" type="number" class="form-control form--control-select mx-auto {{$category->priority == 0 ? 'text-title':''}} {{$category->priority == 1 ? 'text-info':''}} {{$category->priority == 2 ? 'text-success':''}} " onchange="this.form.submit()" value="{{$category->priority}}">
                                                </form>
                                            </td>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-white"
                                                href="{{route('admin.category.edit',[$category['id']])}}" title="{{__('messages.edit')}} {{__('messages.category')}}"><i class="tio-edit"></i>
                                            </a>
                                            <a class="btn btn-sm btn-white" href="javascript:"
                                            onclick="form_alert('category-{{$category['id']}}','Want to delete this category')" title="{{__('messages.delete')}} {{__('messages.category')}}"><i class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{route('admin.category.delete',[$category['id']])}}" method="post" id="category-{{$category['id']}}">
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
                                    {!! $categories->links() !!}
                                </div>
                            </div>
                        </div>
                        <!-- End Pagination -->
                    </div>
                </div>
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
@endpush
