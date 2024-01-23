@extends('layouts.admin.app')

@section('title','Delivery Man Preview')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('messages.dashboard')}}</a></li>
                <li class="breadcrumb-item" aria-current="page">{{__('messages.deliveryman')}} {{__('messages.view')}}</li>
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
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'info'])}}"  aria-disabled="true">{{__('messages.info')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'profile'])}}"  aria-disabled="true">{{__('messages.profile')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'transaction'])}}"  aria-disabled="true">{{__('messages.transaction')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'order']) }}"
                                aria-disabled="true">{{ __('messages.orders') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'suspension'])}}"  aria-disabled="true">{{__('messages.suspension_logs')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'timelog'])}}"  aria-disabled="true">{{__('messages.time_logs')}}</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'online_percentage'])}}"  aria-disabled="true">{{__('messages.online_percentage')}}</a>
                        </li>
                    </ul>
                    <!-- End Nav -->
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <!-- Card -->
        <div class="card mb-3 mb-lg-5 mt-2">
            <div class="card-header">
                <div class="search--btn-wrapper">
                    <h3 class="qcont px-3 m-0 mr-auto">{{ __('messages.time_logs')}}</h3>
                    <form method="GET" action="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'online_percentage'])}}">
                        <div class="input-group">
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
                    <!-- Unfold -->
                    <div class="hs-unfold mr-2 justify-content-end">
                        <a class="js-hs-unfold-invoker btn btn-white dropdown-toggle" href="javascript:;"
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
                </div>
            </div>

            <!-- Body -->
            <div class="card-body">
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
                                <th>{{__('messages.date')}}</th>
                                <th>{{__('messages.online_time')}}</th>
                                <th>{{__('messages.ilde_time')}}</th>
                                <th>{{__('messages.on_time_delivery')}}</th>
                                <th>{{__('messages.late_delivery')}}</th>
                                <th>{{__('messages.late_pickup')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @foreach($timelogs as $k=>$log)
                            <tr>
                                <td scope="row">{{$log->date}}</td>
                                @php($hrs = (intdiv($log->online_time, 60))  == 0?'':intdiv($log->online_time, 60).' Hrs ')
                                {{-- {{dd($hrs)}} --}}
                                @php($online_time = $hrs. ' ' .($log->online_time % 60).' mins')
                                <td>{{$online_time}}</td>
                                @php($hrs = (intdiv($log->idle_time, 60))  == 0?'':intdiv($log->idle_time, 60).' Hrs ')
                                @php($idle_time =$hrs. ' ' .($log->idle_time % 60).' mins')
                                <td>{{$idle_time}}</td>
                                <td>
                                    {{$log->on_time_delivery}}
                                </td>
                                <td>
                                    {{$log->late_delivery}}
                                </td>
                                <td>{{$log->late_pickup}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Body -->
            <div class="card-footer">
                {!!$timelogs->links()!!}
            </div>
        </div>
        <!-- End Card -->
    </div>
@endsection

@push('script_2')
<script>
    function request_alert(url, message) {
        Swal.fire({
            title: 'Are you sure?',
            text: message,
            type: 'warning',
            showCancelButton: true,
            cancelButtonColor: 'default',
            confirmButtonColor: '#FC6A57',
            cancelButtonText: 'No',
            confirmButtonText: 'Yes',
            reverseButtons: true
        }).then((result) => {
            if (result.value) {
                location.href = url;
            }
        })
    }


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

</script>
@endpush
