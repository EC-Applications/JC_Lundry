@foreach($item_list as $key=>$item)
<tr>
    {{-- <td>{{$key+$item_list->firstItem()}}</td> --}}
    <td>{{$item->id}}</td>
    <td>
        
        <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer"
                src="{{asset('storage/app/public/laundry-items')}}/{{$item['icon']}}" alt=""/>
    </td>
    <td>
        <span class="d-block font-size-sm text-body">
            {{Str::limit($item['name'], 20,'...')}}
        </span>
    </td>
    <td>
        <label class="toggle-switch toggle-switch-sm" for="stocksCheckbox{{$item->id}}">
        <input type="checkbox" onclick="location.href='{{route('admin.laundry.item.status',[$item['id'],$item->status?0:1])}}'"class="toggle-switch-input" id="stocksCheckbox{{$item->id}}" {{$item->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td>
        <a class="btn btn-sm btn-white"
            href="{{route('admin.laundry.item.edit',[$item['id']])}}" title="{{__('messages.edit')}} {{__('messages.item')}}"><i class="tio-edit"></i>
        </a>
        <a class="btn btn-sm btn-white" href="javascript:"
        onclick="form_alert('item-{{$item['id']}}','Want to delete this item')" title="{{__('messages.delete')}} {{__('messages.item')}}"><i class="tio-delete-outlined"></i>
        </a>
        <form action="{{route('admin.laundry.item.delete',[$item['id']])}}" method="post" id="item-{{$item['id']}}">
            @csrf @method('delete')
        </form>
    </td>
</tr>
@endforeach