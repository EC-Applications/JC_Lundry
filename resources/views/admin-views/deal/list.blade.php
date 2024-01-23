@extends('layouts.admin.app')

@section('title',trans('messages.deals'))

@push('css_or_js')

@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-filter-list"></i> {{__('messages.deals')}}</h1>
                </div>

                <div class="col-sm-auto">
                    <a class="btn btn-primary text-capitalize" href="{{route('admin.deals.create')}}">
                        <i class="tio-add-circle"></i> {{__('messages.add_new_deal')}}
                    </a>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <!-- Card -->
                <div class="card">
                    <div class="card-header">
                        <h5>{{__('messages.deal')}} {{__('messages.list')}}<span class="badge badge-soft-dark ml-2" id="itemCount">{{$deals->total()}}</span></h5>
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
                               class="font-size-sm table table-borderless table-thead-bordered table-nowrap table-align-middle card-table"
                               data-hs-datatables-options='{
                                 "order": [],
                                 "orderCellsTop": true,
                                 "paging":false
                               }'>
                               <thead class="thead-light">
                            <tr>
                                <th>{{__('messages.#')}}</th>
                                <th >{{__('messages.title')}}</th>
                                <th >{{__('messages.start_at')}}</th>
                                <th >{{__('messages.end_at')}}</th>
                                <th >{{__('messages.price')}}</th>
                                <th >{{__('messages.restaurant')}}</th>
                                <th>{{__('messages.status')}}</th>
                                <th>{{__('messages.action')}}</th>
                            </tr>

                            </thead>

                            <tbody id="set-rows">
                            @foreach($deals as $key=>$deal)
                                <tr>
                                    <td>{{$key+$deals->firstItem()}}</td>
                                    <td>
                                        <span class="d-block text-body">
                                            {{-- <a href="{{route('admin.deals.show',['deal'=>$deal->id])}}"></a> --}}
                                            {{Str::limit($deal['title'],25,'...')}}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="bg-gradient-light text-dark">{{$deal->start_at ? date('d/M/Y '.config('timeformat'), strtotime($deal->start_at)): 'N/A'}}</span>
                                    </td>
                                    <td>
                                        <span class="bg-gradient-light text-dark">{{$deal->end_at ? date('d/M/Y '.config('timeformat'), strtotime($deal->end_at)): 'N/A'}}</span>
                                    </td>
                                    <td>{{\App\CentralLogics\Helpers::format_currency($deal->price)}}</td>
                                    <td><label class="badge badge-soft-primary"><a href="{{route('admin.vendor.view', $deal->restaurant_id)}}" alt="view restaurant">{{Str::limit($deal->restaurant?$deal->restaurant->name:__('messages.Restaurant deleted!'),20,'...')}}</a></label></td>
                                    <td>
                                        <label class="toggle-switch toggle-switch-sm" for="dealCheckbox{{$deal->id}}">
                                            <input type="checkbox" onclick="location.href='{{route('admin.deal.status',['id'=>$deal['id'],'status'=>$deal->status?0:1])}}'"class="toggle-switch-input" id="dealCheckbox{{$deal->id}}" {{$deal->status?'checked':''}}>
                                            <span class="toggle-switch-label">
                                                <span class="toggle-switch-indicator"></span>
                                            </span>
                                        </label>
                                    </td>
                                    <td>
                                        <a class="btn btn-sm btn-white"
                                            href="{{route('admin.deals.edit',['deal'=>$deal->id])}}" title="{{__('messages.edit')}} {{__('messages.deal')}}"><i class="tio-edit"></i>
                                        </a>
                                        <a class="btn btn-sm btn-white text-danger" href="javascript:"
                                            onclick="form_alert('deal-{{$deal['id']}}','Want to delete this item ?')" title="{{__('messages.delete')}} {{__('messages.deal')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{route('admin.deals.destroy',[$deal['id']])}}"
                                                      method="post" id="deal-{{$deal['id']}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                        <hr>
                        <table class="page-area">
                            <tfoot> 
                            {!! $deals->links() !!}
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
        $(document).on('ready', function () {
            // INITIALIZATION OF DATATABLES
            // =======================================================
            var datatable = $.HSCore.components.HSDatatables.init($('#columnSearchDatatable'));

            $('#column1_search').on('keyup', function () {
                datatable
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
                url: '{{route('admin.deal.search')}}',
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
    </script>
@endpush