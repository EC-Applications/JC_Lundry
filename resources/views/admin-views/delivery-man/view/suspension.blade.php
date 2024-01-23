@extends('layouts.admin.app')

@section('title','Delivery Man Preview')

@push('css_or_js')
<style>
    .initial-6 {
        max-width: 260px;
        white-space: initial !important;
    }
</style>
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
                    {{-- <a href="{{url()->previous()}}" class="btn btn-primary ">
                        <i class="tio-back-ui"></i> {{__('messages.back')}}
                    </a> --}}
                    <button class="btn btn-primary float-right" data-toggle="modal"
                        data-target="#suspensionModal">
                        <i class="tio-add-circle-outlined"></i> {{ __('messages.add_suspension') }}
                    </button>
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
                            <a class="nav-link" href="{{ route('admin.delivery-man.preview', ['id' => $dm->id, 'tab' => 'order']) }}" aria-disabled="true">{{ __('messages.orders') }}</a>
                        </li>
                        <li class="nav-item active">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'suspension'])}}"  aria-disabled="true">{{__('messages.suspension_logs')}}</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'timelog'])}}"  aria-disabled="true">{{__('messages.time_logs')}}</a>
                        </li>
                        <li class="nav-item">
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
                <h3 class="qcont px-3">{{ __('messages.suspension_logs')}}</h3>
                {{-- <div class="col-sm-auto" style="width: 306px;" >
                    <input type="date" class="form-control" onchange="set_filter('{{route('admin.delivery-man.preview',['id'=>$dm->id, 'tab'=> 'transaction'])}}',this.value, 'date')" value="{{$date}}">
                </div> --}}
            </div>
            <!-- Body -->
            <div class="card-body">
                <div class="table-responsive">
                    <table id="datatable"
                        class="table table-borderless table-thead-bordered table-nowrap justify-content-between table-align-middle card-table"
                        style="width: 100%">
                        <thead class="thead-light">
                            <tr>
                                <th>{{__('messages.sl#')}}</th>
                                <th>{{__('messages.suspension_begins')}}</th>
                                <th>{{__('messages.suspension_end')}}</th>
                                <th>{{__('messages.suspension_date')}}</th>
                                <th style="width: 40%;">{{trans('messages.suspension_details')}}</th>
                                <th>{{trans('messages.action')}}</th>
                            </tr>
                        </thead>
                        <tbody>
                        @php($suspension_logs = \App\Models\SuspensionLog::where('delivery_man_id', $dm->id)
                        ->when($date, function($query)use($date){
                            return $query->whereDate('created_at', $date);
                        })->paginate(25))
                        @foreach($suspension_logs as $k=>$sp_log)
                            <tr>
                                <td scope="row">{{$k+$suspension_logs->firstItem()}}</td>
                                <td>
                                    <span class="bg-gradient-light text-dark">{{$sp_log->suspension_start ? date('d/M/Y '.config('timeformat'), strtotime($sp_log->suspension_start)): 'N/A'}}</span>
                                </td>
                                <td>
                                    <span class="bg-gradient-light text-dark">{{$sp_log->suspension_end ? date('d/M/Y '.config('timeformat'), strtotime($sp_log->suspension_end)): 'N/A'}}</span>
                                </td>
                                <td>{{$sp_log->created_at->format('Y-m-d')}}</td>
                                <td class="initial-6">{{$sp_log->details}}</td>
                                <td>
                                    <a class="btn btn-sm btn-white text-danger" href="javascript:" onclick="form_alert('suspension-log-{{$sp_log->id}}','Want to remove this suspension ?')" title="{{__('messages.delete')}}"><i class="tio-delete-outlined"></i>
                                    </a>
                                    <form action="{{route('admin.delivery-man.suspension-delete',['suspension_id'=>$sp_log->id])}}" method="post" id="suspension-log-{{$sp_log->id}}">
                                        @csrf @method('delete')
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <!-- End Body -->
            <div class="card-footer">
                {!!$suspension_logs->links()!!}
            </div>
        </div>
        <!-- End Card -->
    </div>
    <div class="modal fade" id="suspensionModal" tabindex="-1">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-light border-bottom py-3">
                    <h5 class="modal-title flex-grow-1 text-center">{{ __('messages.create_supension') }}</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <form id='suspensionCreate' action="{{ route('admin.delivery-man.suspension-create') }}" method="POST">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="id" value="{{ $dm->id }}">
                        <div class="form-group">
                            <label class="input-label" for="title">{{ trans('messages.suspension_begins') }}</label>
                            <input type="datetime-local" id="date_from" class="form-control" name="suspension_start"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="input-label" for="title">{{ trans('messages.suspension_end') }}</label>
                            <input type="datetime-local" id="date_to" class="form-control" name="suspension_end"
                                required>
                        </div>
                        <div class="form-group">
                            <label class="input-label" for="title">{{ trans('messages.suspension_details') }}</label>
                                <textarea name="details" id="" class="form-control" cols="30" rows="10"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-sm btn-primary" type="submit">
                            {{ trans('messages.Submit') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
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
