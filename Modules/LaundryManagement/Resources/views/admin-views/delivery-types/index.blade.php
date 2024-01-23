@extends('layouts.admin.app')

@section('title',trans('messages.laundry_delivery_types'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-add-circle-outlined"></i> {{__('messages.add_new_laundry_delivery_types')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-body">
                        <form action="{{route('admin.laundry.delivery-type.store')}}" method="post" enctype="multipart/form-data">
                            @csrf

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.title')}}</label>
                                        <input type="text" name="title" class="form-control" placeholder="{{__('messages.new_delivery_type_name')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.duration')}} ({{ __('messages.days') }})</label>
                                        <input type="text" name="duration" class="form-control" placeholder="{{__('messages.duration_in_day')}}" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.charge')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</label>
                                        <input type="text" name="charge" class="form-control" placeholder="{{__('messages.charge')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})" required>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card">
                    <div class="card-header">
                        <h5>{{__('messages.laundry_delivery_types')}} {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$types->count()}}</span></h5>
                        <form id="search-form">
                            @csrf
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch" type="search" name="search" class="form-control" placeholder="{{__('messages.search_here')}}" aria-label="{{__('messages.search_here')}}">
                                <button type="submit" class="btn btn-light">{{__('messages.search')}}</button>
                            </div>
                            <!-- End Search -->
                        </form>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                               class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                "order": [],
                                "orderCellsTop": true,
                                "search": "#datatableSearch",
                                "entries": "#datatableEntries",
                                "isResponsive": false,
                                "isShowPaging": false,
                                "paging": false,
                               }'>
                            <thead class="thead-light">
                                <tr>
                                    <th>{{__('messages.#')}}</th>
                                    <th>{{__('messages.title')}}</th>
                                    <th>{{__('messages.duration')}} ({{ __('messages.days') }})</th>
                                    <th>{{__('messages.charge')}} ({{\App\CentralLogics\Helpers::currency_symbol()}})</th>
                                    <th>{{__('messages.status')}}</th>
                                    <th>{{__('messages.action')}}</th>
                                </tr>
                            </thead>

                            <tbody id="set-rows">
                            @foreach($types as $key=>$type)
                                <tr>
                                    <td>{{$key+$types->firstItem()}}</td>

                                    <td>{{Str::limit($type['title'], 25, '...')}}</td>
                                    <td>{{$type['duration']}} (Days)</td>
                                    <td>{{\App\CentralLogics\Helpers::format_currency($type['charge'])}}</td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$type->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.laundry.delivery-type.status',[$type['id'],$type->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$type->id}}" {{$type->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-white" href="{{route('admin.laundry.delivery-type.edit',[$type['id']])}}"title="{{__('messages.edit')}} {{__('messages.delivery-type')}}"><i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('delivery-type-{{$type['id']}}','Want to delete this delivery type ?')" title="{{__('messages.delete')}} {{__('messages.delivery-type')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{route('admin.laundry.delivery-type.delete',[$type['id']])}}"
                                                    method="post" id="delivery-type-{{$type['id']}}">
                                                @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <div class="page-area">
                            <table>
                                <tfoot>
                                    {!! $types->links() !!}
                                </tfoot>
                            </table>
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
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'), {
                select: {
                    style: 'multi',
                    classMap: {
                        checkAll: '#datatableCheckAll',
                        counter: '#datatableCounter',
                        counterInfo: '#datatableCounterInfo'
                    }
                },
                language: {
                    zeroRecords: '<div class="text-center p-4">' +
                    '<img class="mb-3" src="{{asset('public/assets/admin/svg/illustrations/sorry.svg')}}" alt="Image Description" style="width: 7rem;">' +
                    '<p class="mb-0">No data to show</p>' +
                    '</div>'
                }
            });

            $('#datatableSearch').on('mouseup', function (e) {
                var $input = $(this),
                    oldValue = $input.val();

                if (oldValue == "") return;

                setTimeout(function(){
                    var newValue = $input.val();

                    if (newValue == ""){
                    // Gotcha
                    datatable.search('').draw();
                    }
                }, 1);
            });

            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
    <script>
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.laundry.delivery-type.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('#itemCount').html(data.count);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
@endpush
