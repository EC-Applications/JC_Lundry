
   @extends('layouts.admin.app')

   @section('title','Activity Log')

   @push('css_or_js')
   <meta name="csrf-token" content="{{ csrf_token() }}">
   <style>
       .select2-container--default.select2-container--focus .select2-selection--multiple {
           border-color: transparent;
       }
       .select2-container--default .select2-selection--multiple {
           border-color: #e7eaf300;
           padding: 0 .875rem;
       }
   </style>
@endpush

   @section('content')
       <div class="content container-fluid">
           <!-- Page Header -->
           <div class="page-header">
               <div class="row">
                   <div class="col-6">
                       <h1 class="page-header-title text-break">{{__('messages.activity_log')}}</h1>
                   </div>
                   {{-- <div class="col-6">
                       <a href="{{route('vendor.food.edit',[$product['id']])}}" class="btn btn-primary float-right">
                           <i class="tio-edit"></i> {{__('messages.edit')}}
                       </a>
                   </div> --}}
               </div>
           </div>
           <!-- End Page Header -->
        <!-- Card -->
    <div class="card">
        <div class="card-header">
            <div class="btn--container w-100">
                    <form action="{{route('admin.activity-log')}}"style="flex-grow: 1">
                        <!-- Search -->
                        <div class="input-group input-group-merge input-group-flush">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <i class="tio-search"></i>
                                </div>
                            </div>
                            <input id="datatableSearch_" type="search" name="search" class="form-control" value="{{request()->get('search')}}"
                                   placeholder="{{__('messages.search')}}" aria-label="Search" required>
                            <button type="submit" class="btn btn-primary">{{__('messages.search')}}</button>
                            @if(request()->get('search'))
                            <button type="reset" class="btn btn-info mx-1" onclick="location.href = '{{route('admin.activity-log')}}'">{{__('messages.reset')}}</button>
                            @endif
                        </div>
                        <!-- End Search -->
                    </form>
                    <!-- <form action="javascript:" id="search-form"> -->
                    <form action="{{route('admin.activity-log')}}" style="flex-grow: 1">
                        <!-- Search -->

                            <div class="row align-items-end">
                                <div class="col-sm-3">
                                    <input type="date" name="start_date" value="{{old('start_date')}}" class="form-control" id="start_date" data-toggle="tooltip" data-placement="bottom" title="{{ __('messages.select_start_date') }}"
                                         >
                                </div>
                                <div class="col-sm-3">
                                    <input type="date" name="end_date" value="{{old('end_date')}}" class="form-control" id="end_date" data-toggle="tooltip" data-placement="bottom" title="{{ __('messages.select_end_date') }}"
                                           >
                                </div>
                                <div class="col-6 col-sm-4 mt-2" style="max-width:190px">
                                    <select name="restaurant_id" data-placeholder="{{__('messages.select')}} {{__('messages.restaurant')}}" class="js-data-example-ajax form-control"
                                   oninvalid="this.setCustomValidity('{{__('messages.please_select_restaurant')}}')">
                                    @if(isset($restaurant))
                                    <option value="{{$restaurant->id}}" selected="selected">{{$restaurant->name}}</option>
                                    @endif
                                    </select>

                               </div>
                                    <div class="col-4 col-sm-3 mt-2">

                                        <button type="submit" class="btn btn-info mx-1" onclick="location.href = '{{route('admin.activity-log')}}'">{{__('messages.submit')}}</button>
                                    </div>
                            </div>

                        <!-- End Search -->
                    </form>


                        <!-- Unfold -->

                        <!-- End Unfold -->
                    <div class="hs-unfold mr-2">
                        <a class="js-hs-unfold-invoker btn btn-sm btn-white dropdown-toggle" href="javascript:;"
                           data-hs-unfold-options='{
                             "target": "#usersExportDropdown",
                             "type": "css-animation"
                           }'>
                            <i class="tio-download-to mr-1"></i> {{__('messages.export')}}
                        </a>

                        <div id="usersExportDropdown"
                             class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-sm-right">
                            <span class="dropdown-header">{{__('messages.options')}}</span>
                            <a id="export-copy" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                     src="{{asset('public/assets/admin')}}/svg/illustrations/copy.svg"
                                     alt="Image Description">
                                {{__('messages.copy')}}
                            </a>
                            <a id="export-print" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                     src="{{asset('public/assets/admin')}}/svg/illustrations/print.svg"
                                     alt="Image Description">
                                {{__('messages.print')}}
                            </a>
                            <div class="dropdown-divider"></div>
                            <span class="dropdown-header">{{__('messages.download')}} {{__('messages.options')}}</span>
                            <a id="export-excel" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                     src="{{asset('public/assets/admin')}}/svg/components/excel.svg"
                                     alt="Image Description">
                                {{__('messages.excel')}}
                            </a>
                            <a id="export-csv" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                     src="{{asset('public/assets/admin')}}/svg/components/placeholder-csv-format.svg"
                                     alt="Image Description">
                                .{{__('messages.csv')}}
                            </a>
                            <a id="export-pdf" class="dropdown-item" href="javascript:;">
                                <img class="avatar avatar-xss avatar-4by3 mr-2"
                                     src="{{asset('public/assets/admin')}}/svg/components/pdf.svg"
                                     alt="Image Description">
                                {{__('messages.pdf')}}
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- Table -->
        <div class="table-responsive datatable-custom">
            <table id="datatable"
            class="table table-hover table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
            style="width: 100%"
            data-hs-datatables-options='{
          "columnDefs": [{
             "targets": [0],
             "orderable": false
           }],
          "order": [],
          "info": {
            "totalQty": "#datatableWithPaginationInfoTotalQty"
          },
          "search": "#datatableSearch",
          "entries": "#datatableEntries",
          "isResponsive": false,
          "isShowPaging": false,
          "paging": false
        }'>
                <thead class="thead-light">
                <tr>
                    <th>{{__('messages.sl')}}</th>
                    <th>{{__('messages.changed_by')}}</th>
                    <th>{{__('messages.user_type')}}</th>
                    <th>{{__('messages.food_name')}}</th>
                    <th>{{__('messages.changed_data')}}</th>
                    <th>{{__('messages.date')}}</th>
                </tr>
                </thead>

                <tbody>

                @foreach($logs as $key => $log)
                    <tr>
                        <td>{{ $key+$logs->firstItem() }}</td>
                        <td>
                            {{$log->logable?$log->logable->f_name.' '.$log->logable->l_name:trans('messages.user_not_found')}}
                        </td>
                        <td>
                            {{-- {{$log->logable_type=="App\Models\Vendor"?trans('messages.restaurant_owner'):trans('messages.Employee')}}
                            --}}
                            @if($log->logable_type=="App\Models\Admin")
                                @if ($log->logable->role_id=='1')
                                {{trans('messages.admin')}}
                                @else
                                {{trans('messages.admin_employee')}}
                                @endif
                            @elseif ($log->logable_type=="App\Models\Vendor")
                            {{trans('messages.restaurant_owner')}}
                            @else
                            {{trans('messages.restaurant_employee')}}
                            @endif
                        </td>
                        {{-- {{ dd(json_decode($log->current_state['variations'],true)) }} --}}
                        <td>{{$log->model =='Food' ?(isset($log->food)?$log->food->name. ' ':trans('messages.food_not_found')):trans('messages.not_found')}}</td>
                        <td>
                            <div style="max-width:230px; white-space: initial; word-break: break-all;">
                                @foreach($log->current_state as $key=>$value)
                                    @if(!in_array($key,['category_ids','updated_at','choice_options','attributes']))
                                        @if ($key=='variations')
                                            <span><strong>{{trans("messages.{$key}")}}:</strong></span><br>
                                            @foreach(json_decode($value,true) as $variation)
                                                <span class="text-capitalize">
                                                {{$variation['type']}} : {{\App\CentralLogics\Helpers::format_currency($variation['price'])}}
                                                </span><br>
                                            @endforeach
                                        @else
                                            <span><strong>{{trans("messages.{$key}")}}:</strong>{{$value}}</span><br>
                                        @endif

                                    @endif

                                @endforeach
                            </div>

                        </td>
                        <td>
                            {{date('d M Y '.config('timeformat'),strtotime($log['created_at']))}}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
        <!-- End Table -->

        <!-- Footer -->
        <div class="card-footer">
            <!-- Pagination -->
            <div class="row justify-content-center justify-content-sm-between align-items-sm-center">
                <div class="col-12">
                    {!! $logs->links() !!}
                </div>
            </div>
            <!-- End Pagination -->
        </div>
        <!-- End Footer -->
    </div>
    <!-- End Card -->
       </div>
   @endsection

   @push('script_2')
   <script>
    $(document).on('ready', function () {
        // INITIALIZATION OF SELECT2
        // =======================================================
        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });
    });
    $('.js-data-example-ajax').select2({
        ajax: {
            url: '{{url('/')}}/admin/vendor/get-restaurants',
            data: function (params) {
                return {
                    q: params.term, // search term
                    page: params.page
                };
            },
            processResults: function (data) {
                return {
                results: data
                };
            },
            __port: function (params, success, failure) {
                var $request = $.ajax(params);

                $request.then(success);
                $request.fail(failure);

                return $request;
            }
        }
    });

</script>
<script>
    $(document).on('ready', function () {
        // INITIALIZATION OF SELECT2
        // =======================================================
        $('.js-select2-custom').each(function () {
            var select2 = $.HSCore.components.HSSelect2.init($(this));
        });

        var zone_id = [];
        $('#zone_ids').on('change', function(){
            if($(this).val())
            {
                zone_id = $(this).val();
            }
            else
            {
                zone_id = [];
            }
        });


        $('#vendor_ids').select2({
            ajax: {
                url: '{{url('/')}}/admin/vendor/get-restaurants',
                data: function (params) {
                    return {
                        q: params.term, // search term
                        zone_ids: zone_id,
                        page: params.page
                    };
                },
                processResults: function (data) {
                    return {
                    results: data
                    };
                },
                __port: function (params, success, failure) {
                    var $request = $.ajax(params);

                    $request.then(success);
                    $request.fail(failure);

                    return $request;
                }
            }
        });

        // INITIALIZATION OF DATATABLES
        // =======================================================
        var datatable = $.HSCore.components.HSDatatables.init($('#datatable'), {
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'copy',
                    className: 'd-none'
                },
                {
                    extend: 'excel',
                    className: 'd-none'
                },
                {
                    extend: 'csv',
                    className: 'd-none'
                },
                {
                    extend: 'pdf',
                    className: 'd-none'
                },
                {
                    extend: 'print',
                    className: 'd-none'
                },
            ],
            select: {
                style: 'multi',
                selector: 'td:first-child input[type="checkbox"]',
                classMap: {
                    checkAll: '#datatableCheckAll',
                    counter: '#datatableCounter',
                    counterInfo: '#datatableCounterInfo'
                }
            },
            language: {
                zeroRecords: '<div class="text-center p-4">' +
                    '<img class="mb-3" src="{{asset('public/assets/admin')}}/svg/illustrations/sorry.svg" alt="Image Description" style="width: 7rem;">' +
                    '<p class="mb-0">No data to show</p>' +
                    '</div>'
            }
        });

        $('#export-copy').click(function () {
            datatable.button('.buttons-copy').trigger()
        });

        $('#export-excel').click(function () {
            datatable.button('.buttons-excel').trigger()
        });

        $('#export-csv').click(function () {
            datatable.button('.buttons-csv').trigger()
        });

        $('#export-pdf').click(function () {
            datatable.button('.buttons-pdf').trigger()
        });

        $('#export-print').click(function () {
            datatable.button('.buttons-print').trigger()
        });

        $('#datatableSearch').on('mouseup', function (e) {
            var $input = $(this),
                oldValue = $input.val();

            if (oldValue == "") return;

            setTimeout(function () {
                var newValue = $input.val();

                if (newValue == "") {
                    // Gotcha
                    datatable.search('').draw();
                }
            }, 1);
        });

        $('#toggleColumn_order').change(function (e) {
            datatable.columns(1).visible(e.target.checked)
        })

        $('#toggleColumn_date').change(function (e) {
            datatable.columns(2).visible(e.target.checked)
        })

        $('#toggleColumn_customer').change(function (e) {
            datatable.columns(3).visible(e.target.checked)
        })
        $('#toggleColumn_restaurant').change(function (e) {
            datatable.columns(4).visible(e.target.checked)
        })

        $('#toggleColumn_payment_status').change(function (e) {
            datatable.columns(5).visible(e.target.checked)
        })

        $('#toggleColumn_total').change(function (e) {
            datatable.columns(6).visible(e.target.checked)
        })
        $('#toggleColumn_order_status').change(function (e) {
            datatable.columns(7).visible(e.target.checked)
        })

        $('#toggleColumn_actions').change(function (e) {
            datatable.columns(8).visible(e.target.checked)
        })

        // INITIALIZATION OF TAGIFY
        // =======================================================
        $('.js-tagify').each(function () {
            var tagify = $.HSCore.components.HSTagify.init($(this));
        });

        $("#date_from").on("change", function () {
            $('#date_to').attr('min',$(this).val());
        });

        $("#date_to").on("change", function () {
            $('#date_from').attr('max',$(this).val());
        });
    });

    $('#reset').on('click', function(){
        // e.preventDefault();
        location.href = '{{url('/')}}/admin/order/filter/reset';
    });
</script>

   @endpush
