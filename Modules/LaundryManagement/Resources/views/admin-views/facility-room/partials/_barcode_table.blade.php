@if($sectionName == 'processed')
    <div class="card mt-5">
        <div class="card-body">
            <table class="table table-responsive-sm table-responsive-xl">
                <thead class="text-center">
                <tr>
                    <th>{{ trans('messages.item_name') }}</th>
                    <th>{{ trans('messages.bar_code') }}</th>
                    <th>{{ trans('messages.order_id') }}</th>
                    <th>{{ trans('messages.processing_status') }}</th>
                    <th>{{ trans('messages.print') }}</th>
                </tr>
                </thead>
                <tbody class="text-center">
                    @foreach ($order->details as $item)
                        @foreach ($item->item_track as $track)
                            <tr>
                                <td>
                                    <div class="media align-items-center">
                                        <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/laundry-items')}}/{{$item->laundry_item->icon}}" 
                                            onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$item->laundry_item->name}} image">
                                        <div class="media-body">
                                            <h5 class="text-hover-primary mb-0">{{$item->laundry_item->name}}</h5>
                                        </div>
                                    </div>
                                </td>
                                <td id="printarea{{$track->id}}">
                                    <div class="barcodea4">
                                        <div class="item style24">
                                            <span class="barcode_image">
                                                <?php
                                                    echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($track->bar_code, "C128") . '" alt="barcode"   />';
                                                ?>
                                            </span>
                                            <span
                                                class="barcode_code text-capitalize">CODE: {{$track->bar_code}}</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    {{ $item->laundry_orders_id }}
                                </td>
                                <td>
                                    @if($track->processed)
                                        <span class="badge badge-soft-success">
                                        <span class="legend-indicator bg-success"></span>{{__('messages.ready_for_delivery')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-danger">
                                        <span class="legend-indicator bg-danger"></span>{{__('messages.in_processing')}}
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <button onclick="printDiv('printarea{{$track->id}}')" type="button" class="btn btn-danger"><i class="tio-print"></i></button>
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                <tr>

                    <td colspan="4"></td>
                    <td>
                        <button onclick="printDiv('printarea')" class="btn btn btn-block btn-success"><i class="tio-print"></i> {{ trans('messages.print_all') }}</button>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@elseif ($sectionName == 'for_delivery')
    <div class="card mt-5">
        <div class="card-body">
            <table class="table mb-5">
                <thead>
                <tr>
                    <th>{{ trans('messages.your_updated_item') }}</th>
                    <th>{{ trans('messages.order_belongs_to_id') }}</th>
                    <th>{{ trans('messages.processing_status') }}</th>
                </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <tr class="bg-success">
                                <td>
                                    <div class="media align-items-center">
                                        <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/laundry-items')}}/{{$product->details->laundry_item->icon}}" 
                                            onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$product->details->laundry_item->name}} image">
                                        <div class="media-body">
                                            <h5 class="text-white text-hover-primary mb-0">{{$product->details->laundry_item->name}}</h5>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-white font-weight-bold">
                                        <h5 class="text-white text-hover-primary mb-0">{{$order->id}}</h5>
                                    </div>
                                </td>
                                <td>
                                    @if($product->processed)
                                        <span class="badge badge-soft-dark text-white">
                                        <span class="legend-indicator bg-dark"></span>{{__('messages.ready_for_delivery')}}
                                        </span>
                                    @else
                                        <span class="badge badge-soft-dark text-white">
                                        <span class="legend-indicator bg-dark"></span>{{__('messages.in_processing')}}
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table mt-5">
                <thead>
                <tr>
                    <th>{{ trans('messages.other_items') }}</th>
                    <th>{{ trans('messages.processing_status') }}</th>
                </tr>
                </thead>
                <tbody>
                    @foreach ($order->details as $item)
                        @foreach ($item->item_track as $track)
                        @unless ($track->id == $product->id)
                        <tr>
                            <td>
                                <div class="media align-items-center">
                                    <img class="avatar avatar-lg mr-3" src="{{asset('storage/app/public/laundry-items')}}/{{$item->laundry_item->icon}}" 
                                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img2.jpg')}}'" alt="{{$item->laundry_item->name}} image">
                                    <div class="media-body">
                                        <h5 class="text-hover-primary mb-0">{{$item->laundry_item->name}}</h5>
                                    </div>
                                </div>
                            </td>
                            <td>
                                @if($track->processed)
                                    <span class="badge badge-soft-success">
                                    <span class="legend-indicator bg-success"></span>{{__('messages.ready_for_delivery')}}
                                    </span>
                                @else
                                    <span class="badge badge-soft-danger">
                                    <span class="legend-indicator bg-danger"></span>{{__('messages.in_processing')}}
                                    </span>
                                @endif
                            </td>
                        </tr>
                        @endunless
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif



<div id="printarea" style="display: none">
    @foreach ($order->details as $item)
        @foreach ($item->item_track as $track)
            <div class="barcodea4">
                <div class="item style24">
                    <span class="barcode_image">
                        <?php
                            echo '<img src="data:image/png;base64,' . DNS1D::getBarcodePNG($track->bar_code, "C128") . '" alt="barcode"   />';
                        ?>
                    </span>
                    <span
                        class="barcode_code text-capitalize">CODE: {{$track->bar_code}}</span>
                </div>
            </div>
        @endforeach
    @endforeach
</div>



<script>
    function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    var originalContents = document.body.innerHTML;
    document.body.innerHTML = printContents;
    window.print();
    document.body.innerHTML = originalContents;
    location.reload();

}
</script>