@extends('layouts.admin.app')

@section('title','Order List')

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
            <div class="row align-items-center mb-3">
                <div class="col-9">
                    <h1 class="page-header-title text-capitalize">{{__('messages.laundry')}} {{str_replace('_',' ',$status)}} {{__('messages.orders')}} <span
                            class="badge badge-soft-dark ml-2">{{$total}}</span></h1>
                </div>
                
                <div class="col-3" style="width: 306px;">
                    <select name="zone_id" class="form-control js-select2-custom" onchange="set_zone_filter('{{route('admin.laundry.dispatch.list', ['status' => last(request()->segments())])}}', this.value)">
                        <option value="">Select Zone</option>
                        @foreach(Modules\LaundryManagement\Entities\LaundryZone::orderBy('name')->get() as $z)
                            <option
                                value="{{$z['id']}}" {{isset($zone) && $zone->id == $z['id']?'selected':''}}>
                                {{$z['name']}}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
            <!-- End Row -->
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card">
            <!-- Header -->
            <div class="card-header">
                <div class="row justify-content-between align-items-center flex-grow-1">
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <form action="javascript:" id="search-form">
                            <!-- Search -->
                            <div class="input-group input-group-merge input-group-flush">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="tio-search"></i>
                                    </div>
                                </div>
                                <input id="datatableSearch_" type="search" name="search" class="form-control"
                                       placeholder="{{__('messages.search')}}" aria-label="{{__('messages.search')}}" required>
                                <button type="submit" class="btn btn-light">{{__('messages.search')}}</button>

                            </div>
                            <!-- End Search -->
                        </form>
                    </div>

                    <div class="col-lg-6">
                        <div class="d-sm-flex justify-content-sm-end align-items-sm-center">
                            <!-- Datatable Info -->
                            <div id="datatableCounterInfo" class="mr-2 mb-2 mb-sm-0" style="display: none;">
                                <div class="d-flex align-items-center">
                                      <span class="font-size-sm mr-3">
                                        <span id="datatableCounter">0</span>
                                        {{__('messages.selected')}}
                                      </span>
                                    {{--<a class="btn btn-sm btn-outline-danger" href="javascript:;">
                                        <i class="tio-delete-outlined"></i> Delete
                                    </a>--}}
                                </div>
                            </div>
                            <!-- End Datatable Info -->
                            <!-- Unfold -->
                            @if (Request::path() == 'admin/laundry/dispatch/list/searching_for_deliverymen')
                                <div class="hs-unfold mr-2">
                                    <a class="js-hs-unfold-invoker btn btn-sm btn-danger" id="assign_dm">
                                        <i class="tio-hiking mr-1"></i> Assign To <span class="badge badge-success badge-pill ml-1"></span>
                                    </a>
                                </div>
                            @endif
                            <!-- End Unfold -->
                            <!-- Unfold -->
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
                            <!-- End Unfold -->
                            <!-- Unfold -->
                            <div class="hs-unfold mr-2">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white" href="javascript:;"
                                   onclick="$('#datatableFilterSidebar,.hs-unfold-overlay').show(500)">
                                    <i class="tio-filter-list mr-1"></i> Filters <span class="badge badge-success badge-pill ml-1" id="filter_count"></span>
                                </a>
                            </div>
                            <!-- End Unfold -->
                            <!-- Unfold -->
                            <div class="hs-unfold">
                                <a class="js-hs-unfold-invoker btn btn-sm btn-white" href="javascript:;"
                                   data-hs-unfold-options='{
                                     "target": "#showHideDropdown",
                                     "type": "css-animation"
                                   }'>
                                    <i class="tio-table mr-1"></i> {{__('messages.columns')}}
                                </a>

                                <div id="showHideDropdown"
                                     class="hs-unfold-content dropdown-unfold dropdown-menu dropdown-menu-right dropdown-card"
                                     style="width: 15rem;">
                                    <div class="card card-sm">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="mr-2">{{__('messages.order')}}</span>

                                                <!-- Checkbox Switch -->
                                                <label class="toggle-switch toggle-switch-sm" for="toggleColumn_order">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                           id="toggleColumn_order" checked>
                                                    <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                </label>
                                                <!-- End Checkbox Switch -->
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="mr-2">{{__('messages.date')}}</span>

                                                <!-- Checkbox Switch -->
                                                <label class="toggle-switch toggle-switch-sm" for="toggleColumn_date">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                           id="toggleColumn_date" checked>
                                                    <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                </label>
                                                <!-- End Checkbox Switch -->
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="mr-2">{{__('messages.customer')}}</span>

                                                <!-- Checkbox Switch -->
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_customer">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                           id="toggleColumn_customer" checked>
                                                    <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                </label>
                                                <!-- End Checkbox Switch -->
                                            </div>
                                            {{-- <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="mr-2">{{__('messages.restaurant')}}</span>

                                                <!-- Checkbox Switch -->
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_restaurant">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                           id="toggleColumn_restaurant" checked>
                                                    <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                </label>
                                                <!-- End Checkbox Switch -->
                                            </div> --}}

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="mr-2 text-capitalize">{{__('messages.payment')}} {{__('messages.status')}}</span>

                                                <!-- Checkbox Switch -->
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_payment_status">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                           id="toggleColumn_payment_status" checked>
                                                    <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                </label>
                                                <!-- End Checkbox Switch -->
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="mr-2">{{__('messages.total')}}</span>

                                                <!-- Checkbox Switch -->
                                                <label class="toggle-switch toggle-switch-sm" for="toggleColumn_total">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                           id="toggleColumn_total" checked>
                                                    <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                </label>
                                                <!-- End Checkbox Switch -->
                                            </div>
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <span class="mr-2">{{__('messages.order')}} {{__('messages.status')}}</span>

                                                <!-- Checkbox Switch -->
                                                <label class="toggle-switch toggle-switch-sm" for="toggleColumn_order_status">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                           id="toggleColumn_order_status" checked>
                                                    <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                </label>
                                                <!-- End Checkbox Switch -->
                                            </div>

                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="mr-2">{{__('messages.actions')}}</span>

                                                <!-- Checkbox Switch -->
                                                <label class="toggle-switch toggle-switch-sm"
                                                       for="toggleColumn_actions">
                                                    <input type="checkbox" class="toggle-switch-input"
                                                           id="toggleColumn_actions" checked>
                                                    <span class="toggle-switch-label">
                                                    <span class="toggle-switch-indicator"></span>
                                                  </span>
                                                </label>
                                                <!-- End Checkbox Switch -->
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- End Unfold -->
                        </div>
                    </div>
                </div>
                <!-- End Row -->
            </div>
            <!-- End Header -->

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
                        <th class="">
                            <input type="checkbox" id="datatableCheckAll">
                        </th>
                        <th class="">
                            {{__('messages.#')}}
                        </th>
                        <th class="table-column-pl-0">{{__('messages.order')}}</th>
                        <th>{{__('messages.date')}}</th>
                        <th>{{__('messages.customer')}}</th>
                        <th>{{__('messages.payment')}} {{__('messages.status')}}</th>
                        <th>{{__('messages.total')}}</th>
                        <th>{{__('messages.order')}} {{__('messages.status')}}</th>
                        <th>{{__('messages.order')}} {{__('messages.type')}}</th>
                        <th>{{__('messages.actions')}}</th>
                    </tr>
                    </thead>

                    <tbody id="set-rows">
                    @php($order_max_idle_time=\App\Models\BusinessSetting::where('key','order_max_idle_time')->first())
                    @php($order_max_idle_time = $order_max_idle_time?now()->subMinutes($order_max_idle_time->value):now())
                    @foreach($orders as $key=>$order)

                        @php($updated_at = \Carbon\Carbon::parse($order->updated_at))
                        <tr>
                            <td><input type="checkbox" class="sub_chk" data-id="{{$order['id']}}"></td>
                            <td class="">
                                {{$key+$orders->firstItem()}}
                            </td>
                            <td class="table-column-pl-0">
                                <a href="{{route('admin.laundry.order.details',['id'=>$order['id']])}}">{{$order['id']}}</a>
                            </td>
                            <td>{{date('d M Y',strtotime($order['created_at']))}}</td>
                            <td>
                                @if($order->customer)
                                    <a class="text-body text-capitalize"
                                       href="{{route('admin.customer.view',[$order['user_id']])}}">{{$order->customer['f_name'].' '.$order->customer['l_name']}}</a>
                                @else
                                    <label class="badge badge-danger">{{__('messages.invalid')}} {{__('messages.customer')}} {{__('messages.data')}}</label>
                                @endif
                            </td>
                            <td>
                                @if($order->payment_status=='paid')
                                    <span class="badge badge-soft-success">
                                      <span class="legend-indicator bg-success"></span>{{__('messages.paid')}}
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger">
                                      <span class="legend-indicator bg-danger"></span>{{__('messages.unpaid')}}
                                    </span>
                                @endif
                            </td>
                            <td>{{\App\CentralLogics\Helpers::format_currency($order['order_amount'])}}</td>
                            <td class="text-capitalize">
                                @if($order['order_status']=='pending')
                                    <span class="badge badge-soft-info ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-info"></span>{{__('messages.pending')}}
                                    </span>
                                @elseif($order['order_status']=='confirmed')
                                    <span class="badge badge-soft-info ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-info"></span>{{__('messages.confirmed')}}
                                    </span>
                                @elseif($order['order_status']=='processing')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-warning"></span>{{__('messages.processing')}}
                                    </span>
                                @elseif($order['order_status']=='out_for_pickup')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-warning"></span>{{__('messages.out_for_pickup')}}
                                    </span>
                                @elseif($order['order_status']=='picked_up')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-warning"></span>{{__('messages.picked_up')}}
                                    </span>
                                @elseif($order['order_status']=='arrived')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-warning"></span>{{__('messages.arrived')}}
                                    </span>
                                @elseif($order['order_status']=='ready_for_delivery')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-warning"></span>{{__('messages.ready_for_delivery')}}
                                    </span>
                                @elseif($order['order_status']=='out_for_delivery')
                                    <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-warning"></span>{{__('messages.out_for_delivery')}}
                                    </span>
                                @elseif($order['order_status']=='delivered')
                                    <span class="badge badge-soft-success ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-success"></span>{{__('messages.delivered')}}
                                    </span>
                                @elseif($order['order_status']=='cancelled')
                                    <span class="badge badge-soft-success ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-success"></span>{{__('messages.cancelled')}}
                                    </span>
                                @elseif($order['order_status']=='failed')
                                    <span class="badge badge-soft-danger ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-danger text-capitalize"></span>{{__('messages.payment')}}  {{__('messages.failed')}}
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger ml-2 ml-sm-3">
                                      <span class="legend-indicator bg-danger"></span>{{str_replace('_',' ',$order['order_status'])}}
                                    </span>
                                @endif
                            </td>
                            <td class="text-capitalize">
                                <span class="badge badge-soft-dark ml-2 ml-sm-3">
                                    <span class="legend-indicator bg-dark"></span>{{is_null($order->laundry_delivery_type_id)?'not found':$order->delivery_type['title']}}
                                </span>
                            </td>
                            <td>
                                <a class="btn btn-sm btn-white" href="{{route('admin.laundry.order.details',['id'=>$order['id']])}}">
                                    <i class="tio-visible"></i> {{__('messages.view')}}</a>
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
                    <div class="col-sm-auto">
                        <div class="d-flex justify-content-center justify-content-sm-end">
                            <!-- Pagination -->
                            {!! $orders->appends($_GET)->links() !!}
                        </div>
                    </div>
                </div>
                <!-- End Pagination -->
            </div>
            <!-- End Footer -->
        </div>
        <!-- End Card -->
        <!-- Order Filter Modal -->
        <div id="datatableFilterSidebar" class="hs-unfold-content_ sidebar sidebar-bordered sidebar-box-shadow" style="display: none">
            <div class="card card-lg sidebar-card sidebar-footer-fixed">
                <div class="card-header">
                    <h4 class="card-header-title">{{__('messages.order')}} {{__('messages.filter')}}</h4>

                    <!-- Toggle Button -->
                    <a class="js-hs-unfold-invoker_ btn btn-icon btn-xs btn-ghost-dark ml-2" href="javascript:;"
                    onclick="$('#datatableFilterSidebar,.hs-unfold-overlay').hide(500)">
                        <i class="tio-clear tio-lg"></i>
                    </a>
                    <!-- End Toggle Button -->
                </div>
                <?php
                $filter_count=0;
                if(isset($zone_ids) && count($zone_ids) > 0) $filter_count += 1;
                if($status=='all')
                {
                    if(isset($orderstatus) && count($orderstatus) > 0) $filter_count += 1;
                    if(isset($scheduled) && $scheduled == 1) $filter_count += 1;
                }

                if(isset($from_date) && isset($to_date)) $filter_count += 1;
                if(isset($order_type)) $filter_count += 1;

                ?>
                <!-- Body -->
                <form class="card-body sidebar-body sidebar-scrollbar" action="{{route('admin.laundry.order.filter')}}" method="POST" id="order_filter_form">
                    @csrf
                    {{-- <small class="text-cap mb-3">{{__('messages.zone')}}</small>

                    <div class="mb-2" style="border: 1px solid #8080803d;border-radius: 5px">
                        <select name="zone[]" id="zone_ids" class="form-control js-select2-custom" multiple="multiple">
                        @foreach(\App\Models\Zone::all() as $zone)
                            <option value="{{$zone->id}}" {{isset($zone_ids)?(in_array($zone->id, $zone_ids)?'selected':''):''}}>{{$zone->name}}</option>
                        @endforeach
                        </select>
                    </div> --}}

                    @if($status == 'all')
                    <small class="text-cap mb-3">{{__('messages.order')}} {{__('messages.status')}}</small>

                    <!-- Custom Checkbox -->
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus2" name="orderStatus[]" class="custom-control-input" {{isset($orderstatus)?(in_array('pending', $orderstatus)?'checked':''):''}} value="pending">
                        <label class="custom-control-label" for="orderStatus2">{{__('messages.pending')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus1" name="orderStatus[]" class="custom-control-input" value="confirmed" {{isset($orderstatus)?(in_array('confirmed', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus1">{{__('messages.confirmed')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus3" name="orderStatus[]" class="custom-control-input" value="processing" {{isset($orderstatus)?(in_array('processing', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus3">{{__('messages.processing')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus4" name="orderStatus[]" class="custom-control-input" value="picked_up" {{isset($orderstatus)?(in_array('picked_up', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus4">{{__('messages.out_for_delivery')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus5" name="orderStatus[]" class="custom-control-input" value="delivered" {{isset($orderstatus)?(in_array('delivered', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus5">{{__('messages.delivered')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus6" name="orderStatus[]" class="custom-control-input" value="returned" {{isset($orderstatus)?(in_array('returned', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus6">{{__('messages.returned')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus7" name="orderStatus[]" class="custom-control-input" value="failed" {{isset($orderstatus)?(in_array('failed', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus7">{{__('messages.failed')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus8" name="orderStatus[]" class="custom-control-input" value="canceled" {{isset($orderstatus)?(in_array('canceled', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus8">{{__('messages.canceled')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus9" name="orderStatus[]" class="custom-control-input" value="refund_requested" {{isset($orderstatus)?(in_array('refund_requested', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus9">{{__('messages.refundRequest')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="orderStatus10" name="orderStatus[]" class="custom-control-input" value="refunded" {{isset($orderstatus)?(in_array('refunded', $orderstatus)?'checked':''):''}}>
                        <label class="custom-control-label" for="orderStatus10">{{__('messages.refunded')}}</label>
                    </div>

                    <hr class="my-4">

                    <div class="custom-control custom-radio mb-2">
                        <input type="checkbox" id="scheduled" name="scheduled" class="custom-control-input" value="1" {{isset($scheduled)?($scheduled==1?'checked':''):''}}>
                        <label class="custom-control-label text-uppercase" for="scheduled">{{__('messages.scheduled')}}</label>
                    </div>
                    @endif
                    {{-- <hr class="my-4">
                    <small class="text-cap mb-3">{{__('messages.order')}} {{__('messages.type')}}</small>
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="take_away" name="order_type" class="custom-control-input" value="take_away" {{isset($order_type)?($order_type=='take_away'?'checked':''):''}}>
                        <label class="custom-control-label text-uppercase" for="take_away">{{__('messages.take_away')}}</label>
                    </div>
                    <div class="custom-control custom-radio mb-2">
                        <input type="radio" id="delivery" name="order_type" class="custom-control-input" value="delivery" {{isset($order_type)?($order_type=='delivery'?'checked':''):''}}>
                        <label class="custom-control-label text-uppercase" for="delivery">{{__('messages.delivery')}}</label>
                    </div>
                    <hr class="my-4"> --}}

                    <small class="text-cap mb-3">{{__('messages.date')}} {{__('messages.between')}}</small>

                    <div class="row">
                        <div class="col-12">
                            <div class="form-group" style="margin:0;">
                                <input type="date" name="from_date" class="form-control" id="date_from" value="{{isset($from_date)?$from_date:''}}">
                            </div>
                        </div>
                        <div class="col-12 text-center">----TO----</div>
                        <div class="col-12">
                            <div class="form-group">
                                <input type="date" name="to_date" class="form-control" id="date_to" value="{{isset($to_date)?$to_date:''}}">
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="card-footer sidebar-footer">
                        <div class="row gx-2">
                            <div class="col">
                                <button type="reset" class="btn btn-block btn-white" id="reset">Clear all filters</button>
                            </div>
                            <div class="col">
                                <button type="submit" class="btn btn-block btn-primary">Save</button>
                            </div>
                        </div>
                    </div>
                    <!-- End Footer -->
                </form>
            </div>
        </div>
        <!-- End Order Filter Modal -->

            <!--Dm assign Modal -->
    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel">{{__('messages.assign')}} {{__('messages.deliveryman')}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5 my-2">
                            <ul class="list-group overflow-auto" style="max-height:400px;">
                                @foreach ($deliveryMen as $dm)
                                    <li class="list-group-item d-flex align-items-center justify-content-between gap-2">
                                        <div class="dm_list d-flex gap-2" role='button' data-id="{{$dm['id']}}">
                                            <img class="avatar avatar-sm avatar-circle mr-1" onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'" src="{{asset('storage/app/public/delivery-man')}}/{{$dm['image']}}" alt="{{$dm['name']}}">
                                            <div class="d-flex flex-column">
                                                <span class="text-black font-weight-bold">{{$dm['name']}}</span>
                                                <span class="dm_list">
                                                    <div class="text-secondary">
                                                        {{ trans('messages.vehicle') }}: {{$dm['vehicle']}}
                                                    </div>
                                                </span>
                                            </div>
                                        </div>

                                        <a class="btn btn-primary btn-xs float-right" onclick="addDeliveryMan({{$dm['id']}})">{{__('messages.assign')}}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                        <div class="col-md-7 modal_body_map">
                            <div class="location-map" id="dmassign-map">
                                <div style="width: 600px; height: 400px;" id="map_canvas"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Modal -->
@endsection

@push('script_2')
    <!-- <script src="{{asset('public/assets/admin')}}/js/bootstrap-select.min.js"></script> -->
    <script>
        $(document).on('ready', function () {
            @if($filter_count>0)
            $('#filter_count').html({{$filter_count}});
            @endif
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
                        '<img class="mb-3" src="{{asset('public/assets/admin/img')}}/map.png" alt="Image Description" style="width: 7rem;">' +
                        '<p class="mb-0">Please select zone first to show data!</p>' +
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
            location.href = '{{url('/')}}/admin/laundry/order/filter/reset';
        });
    </script>

    <script>
        $('#search-form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.laundry.order.search')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.card-footer').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script>
    <script>
        var order_ids = [];
        var totlaCount = 0;

        // Check All Checkbox
        $("#datatableCheckAll").click(function() {
            $('.sub_chk').prop('checked', this.checked);
        });

        // For Assigning DM
        $('#assign_dm').on('click', function() {
            var totalCount = $(".sub_chk:checked").length;
            order_ids = $(".sub_chk:checked").map(function() {
                return $(this).data('id');
            }).get();

            if (totalCount > 0) {
                $('#myModal').modal('show');
            } else {
                toastr.warning('Please select at least one order to assign a rider.', {
                CloseButton: true,
                ProgressBar: true
                });
            }
        });
    </script>
        <script src="https://maps.googleapis.com/maps/api/js?key={{\App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value}}&v=3.45.8"></script>

    <script>
        function addDeliveryMan(id) {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $.ajax({
                type: "POST",
                url: "{{route('admin.laundry.delivery-man.add-delivery-man')}}",
                data: {
                    'delivery_man_id':id,
                    'order_ids': order_ids
                },
                success: function (data) {
                    location.reload();
                    console.log(data)
                    toastr.success('Successfully added', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                error: function (response) {
                    console.log(response);
                    toastr.error(response.responseJSON.message, {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        }

        function last_location_view() {
            toastr.warning('Only available when order is out for delivery!', {
                CloseButton: true,
                ProgressBar: true
            });
        }
    </script>
    
    <script>
        var deliveryMan = <?php echo(json_encode($deliveryMen)); ?>;

        var map = null;
        var myLatlng = new google.maps.LatLng(49.4558236992736, 11.084644538780994);
        var dmbounds = new google.maps.LatLngBounds (null);
        var locationbounds = new google.maps.LatLngBounds (null);
        var dmMarkers = [];
        dmbounds.extend(myLatlng);
        locationbounds.extend(myLatlng);
        var myOptions = {
            center: myLatlng,
            zoom: 13,
            mapTypeId: google.maps.MapTypeId.ROADMAP,

            panControl: true,
            mapTypeControl: false,
            panControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            zoomControl: true,
            zoomControlOptions: {
                style: google.maps.ZoomControlStyle.LARGE,
                position: google.maps.ControlPosition.RIGHT_CENTER
            },
            scaleControl: false,
            streetViewControl: false,
            streetViewControlOptions: {
                position: google.maps.ControlPosition.RIGHT_CENTER
            }
        };
        function initializeGMap() {

            map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);

            var infowindow = new google.maps.InfoWindow();

            map.fitBounds(dmbounds);
            for (var i = 0; i < deliveryMan.length; i++) {
                if(deliveryMan[i].lat)
                {
                    // var contentString = "<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{asset('storage/app/public/delivery-man')}}/"+deliveryMan[i].image+"'></div><div style='float:right; padding: 10px;'><b>"+deliveryMan[i].name+"</b><br/> "+deliveryMan[i].location+"</div>";
                    var point = new google.maps.LatLng(deliveryMan[i].lat, deliveryMan[i].lng);
                    dmbounds.extend(point);
                    map.fitBounds(dmbounds);
                    var marker = new google.maps.Marker({
                        position: point,
                        map: map,
                        title: deliveryMan[i].location,
                        icon: "{{asset('public/assets/admin/img/delivery_boy_map.png')}}"
                    });
                    dmMarkers[deliveryMan[i].id]=marker;
                    google.maps.event.addListener(marker, 'click', (function(marker, i) {
                        return function() {
                            infowindow.setContent("<div style='float:left'><img style='max-height:40px;wide:auto;' src='{{asset('storage/app/public/delivery-man')}}/"+deliveryMan[i].image+"'></div><div style='float:right; padding: 10px;'><b>"+deliveryMan[i].name+"</b><br/> "+deliveryMan[i].location+"</div>");
                            infowindow.open(map, marker);
                        }
                    })(marker, i));
                }

            };
        }
        $(document).ready(function() {

            // Re-init map before show modal
            $('#myModal').on('shown.bs.modal', function(event) {
                var button = $(event.relatedTarget);
                $("#dmassign-map").css("width", "100%");
                $("#map_canvas").css("width", "100%");
            });

            // Trigger map resize event after modal shown
            $('#myModal').on('shown.bs.modal', function() {
                initializeGMap();
                google.maps.event.trigger(map, "resize");
                map.setCenter(myLatlng);
            });


            $('.dm_list').on('click', function() {
                var id = $(this).data('id');
                map.panTo(dmMarkers[id].getPosition());
                map.setZoom(13);
                dmMarkers[id].setAnimation(google.maps.Animation.BOUNCE);
                window.setTimeout(() => {
                    dmMarkers[id].setAnimation(null);
                }, 3);
            });
        })

    </script>
    
@endpush
