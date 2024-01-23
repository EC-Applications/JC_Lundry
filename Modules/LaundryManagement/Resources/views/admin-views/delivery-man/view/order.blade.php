@extends('layouts.admin.app')

@section('title',trans('messages.deliveryman_preview'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">

@endpush

@section('content')
<div class="content container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">{{ __('messages.dashboard') }}</a></li>
            <li class="breadcrumb-item" aria-current="page">{{ __('messages.deliveryman') }} {{ __('messages.view') }}
            </li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <div class="row">
            <div class="col-6">
                <h1 class="page-header-title">{{$dm['f_name'].' '.$dm['l_name']}}</h1>
            </div>
            <div class="col-6">
                <a href="{{url()->previous()}}" class="btn btn-primary float-right">
                    <i class="tio-back-ui"></i> {{__('messages.back')}}
                </a>
            </div>
            <div class="js-nav-scroller hs-nav-scroller-horizontal">
                <!-- Nav -->
                <ul class="nav nav-tabs page-header-tabs">
                    <li class="nav-item">
                        <a class="nav-link" href="{{route('admin.laundry.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'profile'])}}"  aria-disabled="true">{{__('messages.profile')}}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('admin.laundry.delivery-man.preview', ['id' => $dm->id, 'tab' => 'order']) }}" aria-disabled="true">{{ __('messages.orders') }}</a>
                    </li>
                </ul>
                <!-- End Nav -->
            </div>
        </div>
    </div>
    <!-- End Page Header -->
    <!-- Page Heading -->
    <div class="tab-content">
        <div class="tab-pane fade show active" id="order">
            <div class="row pt-2">
                <div class="col-md-12">
                    <div class="card w-100">
                        <div class="card-header">
                            <div>
                                {{__('messages.orders')}} <span class="badge badge-soft-dark ml-2" id="itemCount">{{$orders->total()}}</span>
                            </div>

                            <div class="col-sm-auto" style="min-width: 306px;">
                                <select name="status" class="form-control js-select2-custom"
                                        onchange="set_filter('{{route('admin.laundry.delivery-man.preview',['id'=>$dm->id, 'tab'=> 'order'])}}',this.value, 'status')">
                                        <option value="">All Orders</option>
                                        <option value="pickup" {{$status == 'pickup'? 'selected':''}}>Pickup</option>
                                        <option value="delivery" {{$status == 'delivery'? 'selected':''}}>Delivery</option>
                                        <option value="on_going" {{$status == 'on_going'? 'selected':''}}>On going</option>
                                        <option value="delivered" {{$status == 'delivered'? 'selected':''}}>Delivered</option>
                                        <option value="cancelled" {{$status == 'cancelled'? 'selected':''}}>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        <!-- Card -->
                        <div class="card-body mb-3 mb-lg-5 border-bottom">
                            <div class="row gx-lg-4">
                                <div class="col-sm-6 col-lg-3">
                                    <div class="media" style="cursor: pointer">
                                        <div class="media-body">
                                            <h6 class="card-subtitle">{{__('messages.for')}} {{__('messages.pickup')}}</h6>
                                            <span class="card-title h3">{{$dm['for_picking_up_orders']}}</span>
                                        </div>
                                        <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                        <i class="tio-airdrop"></i>
                                        </span>
                                    </div>
                                    <div class="d-lg-none">
                                        <hr>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-3 column-divider-sm">
                                    <div class="media" style="cursor: pointer">
                                        <div class="media-body">
                                            <h6 class="card-subtitle">{{__('messages.for')}} {{__('messages.delivery')}}</h6>
                                            <span class="card-title h3">{{$dm['for_delivered_orders']}}</span>
                                        </div>
                                        <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                        <i class="tio-checkmark-circle"></i>
                                        </span>
                                    </div>
                                    <div class="d-lg-none">
                                        <hr>
                                    </div>
                                </div>

                                <div class="col-sm-6 col-lg-3 column-divider-lg">
                                    <div class="media" style="cursor: pointer">
                                        <div class="media-body">
                                            <h6 class="card-subtitle">{{__('messages.canceled_or_failed')}}</h6>
                                            <span class="card-title h3">{{$dm['canceled_or_failed_orders']}}</span>
                                        </div>
                                        <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                        <i class="tio-clock"></i>
                                        </span>
                                    </div>
                                    <div class="d-lg-none">
                                        <hr>
                                    </div>
                                </div>
                                <div class="col-sm-6 col-lg-3 column-divider-sm">
                                    <div class="media" style="cursor: pointer">
                                        <div class="media-body">
                                            <h6 class="card-subtitle">{{__('messages.all')}} {{__('messages.delivered')}}</h6>
                                            <span class="card-title h3">{{$dm['delivered_orders_count']}}</span>
                                        </div>
                                        <span class="icon icon-sm icon-soft-secondary icon-circle ml-3">
                                        <i class="tio-table"></i>
                                        </span>
                                    </div>
                                    <div class="d-lg-none">
                                        <hr>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="row d-flex justify-content-around">
                            <div class="col-lg-4">
                                <form method="GET"
                                    action="{{route('admin.laundry.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'order'])}}">
                                    <div class="input-group mb-3">
                                        <input type="text" name="search" value="{{ request('search') }}"
                                            placeholder="Search by id ex.100010" class="form-control" placeholder=""
                                            aria-label="" aria-describedby="basic-addon1">
                                        <div class="input-group-prepend">
                                            <button class="btn btn-primary" type="submit">{{__('messages.search')}}</button>
                                        </div>
                                    </div>
                    
                                </form>
                            </div>
                            <div class="col-auto justify-content-end">
                                    <form method="GET" action="{{route('admin.laundry.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'order'])}}">
                                        <div class="input-group mb-3">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-outline-secondary" disabled type="button">{{__('messages.Date Range')}}</button>
                                            </div>
                                            <input type="date" name="from" value="{{ request('from') }}"
                                                class="form-control" placeholder="" aria-label=""
                                                aria-describedby="basic-addon1">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-outline-secondary" disabled type="button">---</button>
                                            </div>
                                            <input type="date" name="to" value="{{ request('to') }}"
                                                class="form-control" placeholder="" aria-label=""
                                                aria-describedby="basic-addon1">
                                            <div class="input-group-prepend">
                                                <button class="btn btn-primary" type="submit">{{__('messages.Filter')}}</button>
                                            </div>
                                        </div>
                        
                                    </form>
                        
                            </div>
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
                                "pageLength": 25,
                                "isResponsive": false,
                                "isShowPaging": false,
                                "pagination": "datatablePagination"
                            }'>
                                <thead class="thead-light">
                                <tr>
                                    <th class="">
                                        {{__('messages.#')}}
                                    </th>
                                    <th class="table-column-pl-0">{{__('messages.order')}}</th>
                                    <th>{{__('messages.date')}}</th>
                                    <th>{{__('messages.customer')}}</th>
                                    <th>{{__('messages.payment')}} {{__('messages.status')}}</th>
                                    <th>{{__('messages.total')}}</th>
                                    <th>{{__('messages.order')}} {{__('messages.status')}}</th>
                                    <th>{{__('messages.actions')}}</th>
                                </tr>
                                </thead>

                                <tbody id="set-rows">
                                @foreach($orders as $key=>$order)

                                    <tr class="status-{{$order['order_status']}} class-all">
                                        <td class="">
                                            {{$key+ $orders->firstItem()}}
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
                                            @elseif($order['order_status']=='out_for_delivery')
                                                <span class="badge badge-soft-warning ml-2 ml-sm-3">
                                                <span class="legend-indicator bg-warning"></span>{{__('messages.out_for_delivery')}}
                                                </span>
                                            @elseif($order['order_status']=='delivered')
                                                <span class="badge badge-soft-success ml-2 ml-sm-3">
                                                <span class="legend-indicator bg-success"></span>{{__('messages.delivered')}}
                                                </span>
                                            @else
                                                <span class="badge badge-soft-danger ml-2 ml-sm-3">
                                                <span class="legend-indicator bg-danger"></span>{{str_replace('_',' ',$order['order_status'])}}
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-white"
                                           href="{{route('admin.laundry.order.details',['id'=>$order['id']])}}"><i
                                                class="tio-visible"></i> {{__('messages.view')}}</a>
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
                                        {!! $orders->links() !!}
                                    </div>
                                </div>
                            </div>
                            <!-- End Pagination -->
                        </div>
                        <!-- End Footer -->
                        <!-- End Card -->
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@push('script_2')
    <!-- Page level plugins -->
    <script>
        // Call the dataTables jQuery plugin
        $(document).ready(function () {
            $('#dataTable').DataTable();
        });
    </script>
    <script>
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
                    .columns(1)
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function () {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function () {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function () {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
@endpush
