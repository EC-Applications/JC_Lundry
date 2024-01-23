<div>

    <div class="card-header">
        <h3>{{ __('messages.settled_cash_transaction') }}</h3>
        <div class="justify-content-right">
            <form method="GET" action="{{route('admin.delivery-man.preview', ['id'=>$dm->id, 'tab'=> 'transaction', 'sub_tab'=>'cash'])}}">
                <div class="input-group mb-3">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button">{{__('messages.Date Range')}}</button>
                    </div>
                    <input type="date" name="from" value="{{ request('from') }}"
                        class="form-control" placeholder="" aria-label=""
                        aria-describedby="basic-addon1">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-secondary" type="button">---</button>
                    </div>
                    <input type="date" name="to" value="{{ request('to') }}"
                        class="form-control" placeholder="" aria-label=""
                        aria-describedby="basic-addon1">
                    <div class="input-group-prepend">
                        <button class="btn btn-outline-primary" type="submit">{{__('messages.Filter')}}</button>
                    </div>
                </div>

            </form>

        </div>
    </div>

    <div class="table-responsive">
        <table id="datatable" class="table table-thead-bordered table-align-middle card-table" style="width: 100%">
            <thead class="thead-light">
                <tr>
                    <th>{{ trans('messages.sl#') }}</th>
                    <th>{{ trans('messages.amount') }}</th>
                    <th>{{ trans('messages.balance') }}</th>
                    <th>{{ trans('messages.method') }}</th>
                    <th>{{ trans('messages.reference') }}</th>
                    <th>{{ trans('messages.date') }}</th>
                </tr>
            </thead>
            <tbody>
                @php(
    $transactions = \App\Models\AccountTransaction::where('from_id', $dm->id)->whereFromType('deliveryman')
    // ->when($date, function ($query) use ($date) {
    //         return $query->whereDate('created_at', $date);
    //     })
                            ->when(isset($from) && isset($to) && $from != null && $to != null, function ($query) use($from,$to){
                                return $query->whereBetween('created_at', [$from . " 00:00:00", $to . " 23:59:59"]);
                               })
                               ->paginate(25))
                @foreach ($transactions as $k => $transaction)
                    <tr>
                        <td scope="row">{{ $k + $transactions->firstItem() }}</td>
                        <td>{{ \App\CentralLogics\Helpers::format_currency($transaction->amount) }}</td>
                        <td>{{ \App\CentralLogics\Helpers::format_currency($transaction->current_balance) }}</td>
                        <td>{{ $transaction->method }}</td>
                        <td>{{ $transaction->ref }}</td>
                        <td>{{ $transaction->created_at->format('Y-m-d') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
</div>
<div class="card-footer">
    {!! $transactions->links() !!}
</div>
