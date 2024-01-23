@foreach($types as $key=>$type)
<tr>
    <td>{{$loop->index+1}}</td>

    <td>{{Str::limit($type['title'], 25, '...')}}</td>
    <td>{{$type['duration']}}</td>
    <td>{{$type['minimum_delivery_fee']}}</td>
    <td>{{$type['per_km_delivery_fee']}}</td>
    <td>
        <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$type->id}}">
            <input type="checkbox" onclick="location.href='{{route('admin.laundry.delivery-type.status',[$type['id'],$type->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$type->id}}" {{$type->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td>
        <a class="btn btn-sm btn-white" href="{{route('admin.laundry.delivery-type.edit',[$type['id']])}}"title="{{__('messages.edit')}} {{__('messages.delivery-type')}}"><i class="tio-edit"></i>
        </a>
        <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('delivery-type-{{$type['id']}}','Want to delete this delivery type ?')" title="{{__('messages.delete')}} {{__('messages.delivery-type')}}"><i class="tio-delete-outlined"></i>
        </a>
        <form action="{{route('admin.laundry.delivery-type.delete',[$type['id']])}}"
                    method="post" id="delivery-type-{{$type['id']}}">
                @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach