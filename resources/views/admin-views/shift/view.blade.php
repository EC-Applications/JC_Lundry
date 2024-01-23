@extends('layouts.admin.app')

@section('title','Shift Preview')

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row">
                <div class="col-auto">
                    <h1 class="page-header-title text-break">{{$shift['title']}}
                    </h1>
                </div>
                <div class="col-auto d-flex align-items-center">
                    <span class="badge badge-soft-primary" data-toggle="tooltip" data-placement="top" title="Start time - End time">
                        {{$shift->start_time->format(config('timeformat')).' - '.$shift->end_time->format(config('timeformat'))}}
                    </span>
                    <span class="badge badge-soft-success ml-2 ml-sm-3" data-toggle="tooltip" data-placement="top" title="{{trans('messages.zone')}}">
                        {{trans('messages.zone').': '.$shift->zone->name}}
                    </span>
                </div>
            </div>
        </div>
        <!-- End Page Header -->

        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('messages.special') }} {{ __('messages.shift') }} {{ __('messages.list') }}<span class="badge badge-soft-dark ml-2" id="itemCount">{{ count($shift->special_shifts) }}</span></h5>
                        <button type="button" class="btn btn-sm btn-primary mr-2" data-toggle="modal"
                            data-target="#special-shift-store-modal">
                            <i class="tio-add-circle"></i>
                            <span class="text">{{ trans('messages.add_special_shift') }}</span>
                        </button>
                    </div>
                    <!-- Table -->
                    <div class="table-responsive datatable-custom">
                        <table id="columnSearchDatatable"
                            class="font-size-sm table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                            data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                            <thead class="thead-light">
                                <tr>
                                    <th>{{ __('messages.#') }}</th>
                                    <th>{{ __('messages.start_time') }}</th>
                                    <th>{{ __('messages.end_time') }}</th>
                                    <th>{{ __('messages.date') }}</th>
                                    <th>{{ __('messages.action') }}</th>
                                </tr>

                            </thead>

                            <tbody id="set-rows">
                                @foreach ($shift->special_shifts as $key => $data)
                                    <tr>
                                        <td>{{ $key + 1 }}</td>
                                        <td>
                                            <span
                                                class="bg-gradient-light text-dark">{{ date(config('timeformat'), strtotime($data['start_time'])) }}</span>
                                        </td>
                                        <td>
                                            <span class="bg-gradient-light text-dark">
                                                {{ date(config('timeformat'), strtotime($data['end_time'])) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="bg-gradient-light text-dark">{{ $data->special_date ? date('Y/m/d', strtotime($data->special_date)) : 'N/A' }}</span>
                                        </td>
                                        <td>
                                            <a class="btn btn-sm btn-white text-danger" href="javascript:"
                                                onclick="form_alert('{{ $data['id'] }}','Want to delete this item ?')"
                                                title="{{ __('messages.delete') }} {{ __('messages.shift') }}"><i
                                                    class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{ route('admin.shift.delete', [$data['id']]) }}" method="post"
                                                id="{{ $data['id'] }}">
                                                @csrf @method('delete')
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <table class="page-area">
                        </table>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
        <div class="modal fade" id="special-shift-store-modal" tabindex="-1" role="dialog"
            aria-labelledby="exampleModalLabel-{{ $shift->id }}" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="indicator"></div>
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel-{{ $shift->id }}">
                            {{ trans('messages.add') }} {{ trans('messages.special') }} {{ __('messages.shift') }}</h5>
                        <button type="button" class="close" data-dismiss="modal"
                            aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form action="{{ route('admin.shift.store') }}" method="post">
                        <div class="modal-body">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $shift->id }}">
                            <input type="hidden" name="zone_id" value="{{ $shift->zone_id }}">
                            <div class="form-group">
                                <label class="input-label"
                                    for="title">{{ __('messages.date') }}</label>
                                <input type="date" class="form-control" name="date" min="{{now()->format('Y-m-d')}}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="input-label"
                                    for="title">{{ __('messages.start') }}
                                    {{ __('messages.time') }}</label>
                                <input type="time" class="form-control" name="start_time" value="{{$shift->start_time->format("H:i")}}"
                                    required>
                            </div>
                            <div class="form-group">
                                <label class="input-label"
                                    for="title">{{ __('messages.end') }}
                                    {{ __('messages.time') }}</label>
                                <input type="time" class="form-control" name="end_time" value="{{$shift->end_time->format("H:i")}}"
                                    required>
                            </div>

                        </div>
                        <div class="modal-footer">
                            <button type="submit"
                                class="btn btn-primary">{{ __('messages.submit') }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script_2')

@endpush
