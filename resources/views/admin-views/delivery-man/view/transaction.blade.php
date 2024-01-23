@extends('layouts.admin.app')

@section('title', 'Delivery Man Preview')

@push('css_or_js')
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
                    <h1 class="page-header-title">{{ $dm->f_name . ' ' . $dm->l_name }}</h1>
                </div>
                <div class="col-6">
                    <a href="{{ url()->previous() }}" class="btn btn-primary float-right">
                        <i class="tio-back-ui"></i> {{ __('messages.back') }}
                    </a>
                </div>
                <div class="js-nav-scroller hs-nav-scroller-horizontal">
                    <!-- Nav -->
                    <ul class="nav nav-tabs page-header-tabs">
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'info']) }}"
                                aria-disabled="true">{{ __('messages.info') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'profile']) }}"
                                aria-disabled="true">{{ __('messages.profile') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'transaction']) }}"
                                aria-disabled="true">{{ __('messages.transaction') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'order']) }}"
                                aria-disabled="true">{{ __('messages.orders') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'suspension']) }}"
                                aria-disabled="true">{{ __('messages.suspension_logs') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'timelog']) }}"
                                aria-disabled="true">{{ __('messages.time_logs') }}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link"
                                href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'online_percentage']) }}"
                                aria-disabled="true">{{ __('messages.online_percentage') }}</a>
                        </li>
                    </ul>
                    <!-- End Nav -->
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row my-3">

            <!-- Collected Cash Card Example -->
            <div class="for-card col-sm-4 col-6 mb-2">
                <div class="card r shadow h-100 for-card-body-4 badge-dark">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class=" for-card-text font-weight-bold  text-uppercase mb-1">
                                    {{ __('messages.cash_in_hand') }}</div>
                                <div class="for-card-count">
                                    {{ \App\CentralLogics\Helpers::format_currency($dm->wallet ? $dm->wallet->collected_cash : 0.0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settled Cash In Hand Card Example -->
            <div class="for-card col-sm-4 col-6 mb-2">
                <div class="card r shadow h-100 for-card-body-4 badge-secondary">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class=" for-card-text font-weight-bold  text-uppercase mb-1">
                                    {{ __('messages.settled_cash_in_hand') }}</div>
                                <div class="for-card-count">
                                    {{ \App\CentralLogics\Helpers::format_currency($dm->account_transactions_sum_amount ?? 0.0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Total Collected Cash Card Example -->
            <div class="for-card col-sm-4 col-6 mb-2">
                <div class="card for-card-body-2 shadow h-100  badge-light">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="font-weight-bold  text-uppercase for-card-text mb-1">
                                    {{ __('messages.total_collected_cash') }}
                                </div>
                                <div class="for-card-count">
                                    {{ \App\CentralLogics\Helpers::format_currency($dm->wallet ? $dm->wallet->collected_cash + $dm->account_transactions_sum_amount : 0.0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Earned Balance Card Example -->
            <div class="for-card col-sm-4 col-6 mb-2">
                <div class="card for-card-body-2 shadow h-100  badge-dark">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="font-weight-bold  text-uppercase for-card-text mb-1">
                                    {{ __('messages.earning_balance') }}
                                </div>
                                <div class="for-card-count">
                                    {{ \App\CentralLogics\Helpers::format_currency($dm->wallet ? $dm->wallet->total_earning - $dm->wallet->pending_withdraw - $dm->wallet->total_withdrawn : 0.0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settled Earned Balance Card Example -->
            <div class="for-card col-sm-4 col-6 mb-2">
                <div class="card for-card-body-2 shadow h-100  badge-secondary">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="font-weight-bold  text-uppercase for-card-text mb-1">
                                    {{ __('messages.settled_earning_balance') }}
                                </div>
                                <div class="for-card-count">
                                    {{ \App\CentralLogics\Helpers::format_currency($dm->wallet ? $dm->wallet->total_withdrawn : 0.0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Total Earning Card Example -->
            <div class="for-card col-sm-4 col-6 mb-2">
                <div class="card r shadow h-100 for-card-body-4  badge-light">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class=" for-card-text font-weight-bold  text-uppercase mb-1">
                                    {{ __('messages.total_earning') }}</div>
                                <div class="for-card-count">
                                    {{ \App\CentralLogics\Helpers::format_currency($dm->wallet ? $dm->wallet->total_earning : 0.0) }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- Card -->
        <div class="card mb-3 mb-lg-5 mt-2">
            {{-- <div class="card-header">
                <h3 class="qcont px-3">{{ __('messages.order')}} {{ __('messages.transactions')}}</h3>
                <div class="col-sm-auto" style="width: 306px;" >
                    <input type="date" class="form-control" onchange="set_filter('{{route('admin.delivery-man.preview',['id'=>$dm->id, 'tab'=> 'transaction'])}}',this.value, 'date')" value="{{$date}}">
                </div>
            </div> --}}
            <!-- Body -->

            <div class="card-body">
                <!-- Nav -->
                <ul class="nav nav-tabs mb-2 border-bottom">
                    <li class="nav-item">
                        <a class="nav-link text-capitalize {{ $sub_tab == 'order-cash-in-hand' ? 'active' : '' }}"
                            href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'transaction', 'sub_tab' => 'order-cash-in-hand']) }}"
                            aria-disabled="true">{{ __('messages.order_cash_in_hand') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-capitalize {{ $sub_tab == 'cash' ? 'active' : '' }}"
                            href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'transaction', 'sub_tab' => 'cash']) }}"
                            aria-disabled="true">{{ __('messages.settled_cash_transaction') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-capitalize {{ $sub_tab == 'earning' ? 'active' : '' }}"
                            href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'transaction', 'sub_tab' => 'earning']) }}"
                            aria-disabled="true">{{ __('messages.earning') }}</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link text-capitalize {{ $sub_tab == 'earning-settled' ? 'active' : '' }}"
                            href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'transaction', 'sub_tab' => 'earning-settled']) }}"
                            aria-disabled="true">{{ __('messages.earning_settled') }}</a>
                    </li>
                </ul>
                <!-- End Nav -->
                @include("admin-views.delivery-man.partials._transaction-{$sub_tab}")
                {{-- <div class="table-responsive">
                    <table id="datatable"
                        class="table table-borderless table-thead-bordered table-nowrap justify-content-between table-align-middle card-table"
                        style="width: 100%">
                        <thead class="thead-light">
                            <tr>
                                <th>{{__('messages.sl#')}}</th>
                                <th>{{__('messages.order')}} {{__('messages.id')}}</th>
                                <th>{{__('messages.deliveryman')}} {{__('messages.earned')}}</th>
                                <th>{{__('messages.date')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php($digital_transaction = \App\Models\OrderTransaction::where('delivery_man_id', $dm->id)
                        ->when($date, function($query)use($date){
                            return $query->whereDate('created_at', $date);
                        })->paginate(25))
                        @foreach ($digital_transaction as $k => $dt)
                            <tr>
                                <td scope="row">{{$k+$digital_transaction->firstItem()}}</td>
                                <td><a href="{{route('admin.order.details',$dt->order_id)}}">{{$dt->order_id}}</a></td>
                                <td>{{$dt->original_delivery_charge}}</td>
                                <td>{{$dt->created_at->format('Y-m-d')}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div> --}}
                <!-- End Body -->
                {{-- <div class="card-footer">
                {!!$digital_transaction->links()!!}
            </div> --}}
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
        </script>
    @endpush
