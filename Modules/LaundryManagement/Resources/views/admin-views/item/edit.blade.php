@extends('layouts.admin.app')

@section('title',__('messages.update_service'))

@push('css_or_js')

@endpush

@section('content')

    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <h1 class="page-header-title">
                <span class="page-header-icon">
                    <img src="{{ asset('public/assets/admin/img/edit.png') }}" class="w--26" alt="">
                </span>
                <span>
                   <i class="tio-edit"></i> {{ $item->name}} {{ trans('messages.service_setup') }}
                </span>
            </h1>
        </div>
        <!-- End Page Header -->
        <form action="{{ route('admin.laundry.item.update', $item->id) }}" method="post" id="zone_form" class="shadow--card" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row g-2">
                <div class="col-12">
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.name')}}</label>
                        <input type="text" name="name" value="{{$item->name}}" class="form-control" placeholder="{{__('messages.new_item')}}" required>
                    </div>
                    <div class="form-group">
                        <label>{{__('messages.icon')}}</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                        <div class="custom-file">
                            <input type="file" name="icon" id="customFileEg1" class="custom-file-input"
                                    accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                        </div>
                    </div>

                    <center>
                        <img style="width: 30%;border: 1px solid; border-radius: 10px;" id="viewer"
                                src="{{asset('storage/app/public/laundry-items')}}/{{$item['icon']}}" alt=""/>
                    </center>
                    <div class="form-group mb-0 mt-3">
                        <label class="input-label" for="exampleFormControlSelect1"><h5>{{ trans('messages.laundry_services') }}</h5><span
                                class="input-label-secondary"></span></label>
                        <select name="service_id[]" id="choice_services" class="form-control js-select2-custom"
                            multiple="multiple">
                            @php($services_array = \Modules\LaundryManagement\Entities\Services::get()->toArray())
                            @php($services = \Modules\LaundryManagement\Entities\Services::get())
                            @php($selected_services = count($item->services) > 0 ? $item->services->pluck('id')->toArray() : [])
                            @foreach ($services as $service)
                                <option value="{{ $service['id'] }}"
                                    {{ in_array($service['id'], $selected_services) ? 'selected' : '' }}>
                                    {{ $service['name'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="col-md-12">
                    <div class="service_price_options" id="service_price_options">
                        <div class="row gy-1" id="service-label">
                            <div class="col-sm-6">
                                <label for="">{{ trans('messages.services_name') }}</label>
                            </div>
                            <div class="col-sm-6">
                                <label for="">{{ trans('messages.price') }} ({{ \App\CentralLogics\Helpers::currency_symbol() }})</label>
                            </div>
                        </div>
                        @if (count($item->services) > 0)
                            @foreach ($item->services as $service)
                            <div class="row gy-1 service-row" id="service_{{ $service->id }}">
                                    <div class="col-sm-6"><input type="text" class="form-control"
                                        value="{{ $service->name }}"
                                        placeholder="{{ trans('messages.choice_title') }}" readonly>
                                    </div>
                                    <div class="col-sm-6"><input type="number" class="form-control"
                                        name="service_data[{{ $service->id }}][price]" step=".01"
                                        min="0" placeholder="{{ trans('messages.price') }}"
                                        title="{{ trans('messages.price') }}"
                                        value="{{ $service->pivot->price }}" required>
                                    </div>
                            </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-3 justify-content-start">
                <button type="submit" class="btn btn-primary">{{ trans('messages.update') }}</button>
            </div>
        </form>
    </div>

@endsection

@push('script_2')
    <script src="{{ asset('public/assets/admin') }}/js/tags-input.min.js"></script>
    <script>
        function getRequest(route, id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('#' + id).empty().append(data.options);
                },
            });
        }
        function readURL(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
    
                reader.onload = function (e) {
                    $('#viewer').attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }
    
        $("#customFileEg1").change(function () {
            readURL(this);
        });
    </script>
    <script>
        let services = <?php echo json_encode($services_array); ?>;
        let service_count = {{ count($item->services) }};
        if(service_count>0){
            $('#service-label').show();
        }else{
            $('#service-label').hide();
        }
        $('#choice_services').on('change', function() {
            $('#service-label').show();
            var ids = $('.service-row').map(function() {
                return $(this).attr('id').split('_')[1];
            }).get();

            $.each($("#choice_services option:selected"), function(index, element) {
                console.log($(this).val());
                if (ids.includes($(this).val())) {
                    ids = ids.filter(id => id !== $(this).val());
                } else {
                    let name = $('#choice_services option[value="' + $(this).val() + '"]').html();
                    add_more_service_price_option($(this).val(), name.trim());
                }
            });
            console.log(ids)
            if (ids.length > 0) {
                ids.forEach(element => {
                    console.log("service_", 3)
                    $("#service_" + element.trim()).remove();
                });
            }
        });

        function add_more_service_price_option(i, name) {
            let n = name;
            $('#service_price_options').append(
                '<div class="row gy-1 service-row" id="service_' + i +
                '"><div class="col-sm-6"><input type="text" class="form-control" value="' + n +
                '" placeholder="{{ trans('messages.choice_title') }}" readonly></div><div class="col-sm-6"><input type="number" class="form-control" name="service_data[' +
                i +
                '][price]" step=".01" min="0" placeholder="{{ trans('messages.price') }}" title="{{ trans('messages.price') }}" required></div></div>'
            );
        }
    </script>
@endpush
