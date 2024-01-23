    <!-- Card -->
    <div class="card">
        <!-- Table -->
        <div class="table-responsive datatable-custom">
            <table id="datatable" class="table table-borderless table-thead-bordered table-nowrap card-table"
                    data-hs-datatables-options='{
                    "columnDefs": [{
                    "targets": [0, 3, 6],
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
                    <th>{{__('messages.changed_by')}}</th>
                    <th>{{__('messages.user_type')}}</th>
                    <th>{{__('messages.changed_data')}}</th>
                    <th>{{__('messages.date')}}</th>
                </tr>
                </thead>

                <tbody>

                @foreach($logs as $log)
                    <tr>
                        <td>
                            {{$log->logable?$log->logable->f_name.' '.$log->logable->l_name:trans('messages.user_not_found')}}
                        </td>
                        <td>
                            {{$log->logable_type=="App\Models\Vendor"?trans('messages.owner'):trans('messages.Employee')}}
                        </td>
                        <td>
                            @foreach($log->current_state as $key=>$value)
                                @if(!in_array($key,['category_ids','updated_at']))
                                    <span>{{$key}}:{{$value}}</span><br>
                                @endif
                            @endforeach
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