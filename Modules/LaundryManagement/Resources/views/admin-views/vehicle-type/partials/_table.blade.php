@foreach($types as $key=>$type)
<tr>
    <td></td>
    <td>
        <h5 class="text-hover-primary mb-0">{{Str::limit($type['name'], 25, '...')}}</h5>
    </td>
    <td>
        <label class="toggle-switch toggle-switch-sm" for="statusCheckbox{{$type->id}}">
            <input type="checkbox" onclick="location.href='{{route('admin.laundry.vehicle-type.status',[$type['id'],$type->status?0:1])}}'" class="toggle-switch-input" id="statusCheckbox{{$type->id}}" {{$type->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td>
        <a class="btn btn-sm btn-white" href="{{route('admin.laundry.vehicle-type.edit',[$type['id']])}}"title="{{__('messages.edit')}} {{__('messages.vehicle_type')}}"><i class="tio-edit"></i>
        </a>
        <a class="btn btn-sm btn-white" href="javascript:" onclick="form_alert('vehicle-type-{{$type['id']}}','Want to delete this vehicle_type ?')" title="{{__('messages.delete')}} {{__('messages.vehicle_type')}}"><i class="tio-delete-outlined"></i>
        </a>
        <form action="{{route('admin.laundry.vehicle-type.delete',[$type['id']])}}"
                    method="post" id="vehicle-type-{{$type['id']}}">
                @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach