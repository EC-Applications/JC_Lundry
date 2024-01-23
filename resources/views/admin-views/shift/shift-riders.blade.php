@extends('layouts.admin.app')

@section('title', __('messages.shift_wise_riders'))

@push('css_or_js')
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-filter-list"></i> {{ __('messages.shift_wise_riders') }}</h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-primary" href="{{ route('admin.shift.add-new') }}">
                        <i class="tio-add-circle"></i> {{ __('messages.add') }} {{ __('messages.new') }}
                        {{ __('messages.shift') }}
                    </a>
                </div>
            </div>
        </div>
        {{-- <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{ route('admin.shift.list') }}" method="get">
                    <div class="row">
                        <div class="col-md-10">
                            <div class="form-group">
                                <label class="input-label"
                                    for="title">{{ __('messages.select') }}{{ __('messages.zone') }}</label>
                                <select name="zone_id" id="zone" class="form-control js-select2-custom">
                                    <option disabled selected>---{{ __('messages.select') }}---</option>
                                    @php($zones = \App\Models\Zone::all())
                                    @foreach ($zones as $zone)
                                        <option value="{{ $zone['id'] }}"
                                            {{ $zone_id == $zone['id'] ? 'selected' : '' }}>
                                            {{ $zone['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group mt-4">
                                <button type="submit"
                                    class="btn btn-primary form-control">{{ __('messages.submit') }}</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div> --}}
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('messages.shift_wise_riders') }}<span class="badge badge-soft-dark ml-2"
                                id="itemCount">{{ $shifts->total() }}</span></h5>
                        {{-- <form id="search-form">
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
                        </form> --}}
                        @if(!isset(auth('admin')->user()->zone_id))
                        <div class="col-sm-auto" style="min-width: 306px;">
                            <select name="zone_id" class="form-control js-select2-custom"
                                    onchange="set_zone_filter('{{route('admin.shift.shift-riders')}}',this.value)">
                                    <option value="all">Select Zone</option>
                                @foreach(\App\Models\Zone::orderBy('name')->get() as $z)
                                    <option
                                        value="{{$z['id']}}" {{request('zone_id') == $z['id']?'selected':''}}>
                                        {{$z['name']}}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @endif
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
                                    <th>{{ __('messages.shift') }} {{ __('messages.name') }}</th>
                                    <th>{{ __('messages.start_time') }}</th>
                                    <th>{{ __('messages.end_time') }}</th>
                                    <th>{{ __('messages.deliverymans') }}</th>
                                    {{-- <th>{{ __('messages.action') }}</th> --}}
                                </tr>

                            </thead>

                            <tbody id="set-rows">
                                @if(request()->query('zone_id'))
                                    @foreach ($shifts as $key => $shift)
                                    <tr>
                                        <td>{{ $key + $shifts->firstItem() }}</td>
                                        <td>
                                            <span class="d-block text-body"><a
                                                href="{{route('admin.shift.view',[$shift['id']])}}">{{ Str::limit($shift['title'], 25, '...') }}</a>
                                            </span>
                                        </td>
                                        <td>
                                            <span
                                                class="bg-gradient-light text-dark">{{ date(config('timeformat'), strtotime($shift['start_time'])) }}</span>
                                        </td>
                                        <td>
                                            <span class="bg-gradient-light text-dark">
                                                {{ date(config('timeformat'), strtotime($shift['end_time'])) }}</span>
                                        </td>
                                        <td>
                                            <span
                                                class="bg-gradient-light text-dark">{{ $shift->total_dm }}</span>
                                        </td>
                                        {{-- <td>
                                            <a class="btn btn-sm btn-white"
                                                href="{{route('admin.shift.view',[$shift->id])}}" title="{{__('messages.view')}} {{__('messages.shift')}}"><i class="tio-visible text-success"></i>
                                            </a>
                                            <a class="btn btn-sm btn-white"
                                                href="{{route('admin.shift.edit',[$shift->id])}}" title="{{__('messages.edit')}} {{__('messages.shift')}}"><i class="tio-edit text-primary"></i>
                                            </a>
                                            <a class="btn btn-sm btn-white text-danger" href="javascript:"
                                                onclick="form_alert('{{ $shift['id'] }}','Want to delete this item ?')"
                                                title="{{ __('messages.delete') }} {{ __('messages.shift') }}"><i
                                                    class="tio-delete-outlined"></i>
                                            </a>
                                            <form action="{{ route('admin.shift.delete', [$shift['id']]) }}" method="post"
                                                id="{{ $shift['id'] }}">
                                                @csrf @method('delete')
                                            </form>
                                        </td> --}}
                                    </tr>
                            @endforeach
                                @endif
                            </tbody>
                        </table>
                        <hr>
                        <table class="page-area">
                            <tfoot>
                                {!! $shifts->links() !!}
                            </tfoot>
                        </table>
                    </div>
                    <!-- End Table -->
                </div>
                <!-- End Card -->
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script>
        $(document).on('ready', function() {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function() {
                datatable
                    .search(this.value)
                    .draw();
            });

            $('#column2_search').on('keyup', function() {
                datatable
                    .columns(2)
                    .search(this.value)
                    .draw();
            });

            $('#column3_search').on('change', function() {
                datatable
                    .columns(3)
                    .search(this.value)
                    .draw();
            });

            $('#column4_search').on('keyup', function() {
                datatable
                    .columns(4)
                    .search(this.value)
                    .draw();
            });


            // INITIALIZATION OF SELECT2
            // =======================================================
            $('.js-select2-custom').each(function() {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
        });
    </script>
    {{-- <script>
        $('#search-form').on('submit', function (e) {
            e.preventDefault();
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: '{{route('admin.shift.searchItem')}}',
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                beforeSend: function () {
                    $('#loading').show();
                },
                success: function (data) {
                    $('#set-rows').html(data.view);
                    $('.page-area').hide();
                },
                complete: function () {
                    $('#loading').hide();
                },
            });
        });
    </script> --}}
@endpush
