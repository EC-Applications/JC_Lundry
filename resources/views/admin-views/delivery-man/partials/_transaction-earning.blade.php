<div>
    <div class="card-header">
        <h3>{{ __('messages.earning') }} {{ __('messages.transactions') }}</h3>
    </div>
    <div class="row justify-content-around mt-2">
        <div class="col-md-3">
            <form method="GET"
                action="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'transaction', 'sub_tab'=>'earning'])}}">
                <div class="input-group mb-3">
                    <input type="text" name="search" value="{{ request('search') }}"
                        placeholder="Search by id ex.100010" class="form-control" placeholder=""
                        aria-label="" aria-describedby="basic-addon1">
                    <div class="input-group-prepend">
                        <button class="btn btn-primary" type="submit">{{__('messages.Search')}}</button>
                    </div>
                </div>

            </form>
        </div>
        <div class="col-auto justify-content-right">
            <form method="GET" action="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'transaction', 'sub_tab'=>'earning'])}}">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button">{{__('messages.Date Range')}}</button>
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
    {{-- <h3 class="qcont px-3 pt-4">{{ __('messages.earning') }} {{ __('messages.transactions') }}</h3> --}}
    <div class="table-responsive">
        <table id="datatable" class="table table-thead-bordered table-align-middle card-table" style="width: 100%">
            <thead class="thead-light">
                <tr>
                    <th>{{ __('messages.sl#') }}</th>
                    <th>{{ __('messages.order') }} {{ __('messages.id') }}</th>
                    <th>{{ __('messages.total_order_amount') }}</th>
                    <th>{{ __('messages.deliveryman') }} {{ __('messages.earned') }}</th>
                    <th>{{ __('messages.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @php( $key = explode(' ', $search))
                @php(
            $transaction = \App\Models\OrderTransaction::where('delivery_man_id', $dm->id)
    // ->when($date, function ($query) use ($date) {
    //         return $query->whereDate('created_at', $date);
    //     })
                    ->when(isset($from) && isset($to) && $from != null && $to != null, function ($query) use($from,$to){
                            return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
                        })
                        ->when(isset($search), function($query) use($key){
                            foreach ($key as $value) {
                                $query->where('order_id', 'like', "%{$value}%");
                            }
                        })
                      ->paginate(25))
                @foreach ($transaction as $k => $dt)
                    <tr>
                        <td scope="row">{{ $k + $transaction->firstItem() }}</td>
                        <td><a href="{{ route('admin.order.details', $dt->order_id) }}">{{ $dt->order_id }}</a></td>
                        <td>{{ $dt->order_amount }}</td>
                        <td>{{ $dt->original_delivery_charge }}</td>
                        <td>{{ $dt->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
<div class="card-footer">
    {!! $transaction->links() !!}
</div>
