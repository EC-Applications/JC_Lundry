@extends('layouts.admin.app')

@section('title',trans('messages.add_new_deal'))

@push('css_or_js')
    <link href="{{asset('public/assets/admin/css/tags-input.min.css')}}" rel="stylesheet">
    <style>
        .close-icon{
            right: 10px;
            top: 10px;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-capitalize"><i class="tio-add-circle-outlined"></i> {{__('messages.add_new_deal')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="javascript:" method="post" id="deal_form"
                      enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label class="input-label" for="title">{{__('messages.title')}}</label>
                        <input type="text" name="title" class="form-control" placeholder="{{__('messages.deal_title')}}"  maxlength="191">                        
                    </div>
                    <div class="row">
                        <div class="col-sm-3 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlSelect1">{{__('messages.restaurant')}}<span
                                        class="input-label-secondary">*</span></label>
                                <select name="restaurant_id" class="js-data-example-ajax form-control" onchange="getRequest('{{url('/')}}/admin/food/get-foods?selected=1&not_restaurant=1&restaurant_id='+this.value,'choice_item',this.value)"  title="Select Restaurant" required>
                                <option selected>{{trans('messages.select_restaurant')}}</option>
                                    
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3 col-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.price')}}</label>
                                <input type="number" min=".01" max="9999999999" step="0.01" value="1" name="price" class="form-control"
                                       placeholder="Ex : 100" required>
                            </div>
                        </div>

                        <div class="col-sm-3 col-6">
                            <div class="form-group">
                                <label class="input-label" for="title">{{__('messages.start')}} {{__('messages.date')}}</label>
                                <input type="datetime-local" id="date_from" class="form-control" name="start_date" required> 
                            </div>
                        </div>
                  
                        <div class="col-sm-3 col-6">
                            <div class="form-group">
                                <label class="input-label" for="title">{{__('messages.end')}} {{__('messages.date')}}</label>
                                <input type="datetime-local" id="date_to" class="form-control" name="end_date" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.short')}} {{__('messages.description')}}</label>
                                <textarea type="text" name="description" class="form-control" rows="10"></textarea>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label>{{__('messages.food')}} {{__('messages.image')}}</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                                <div class="custom-file">
                                    <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                           accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                    <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                </div>
        
                                <center id="image-viewer-section" class="pt-2">
                                    <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                         src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}" alt="banner image"/>
                                </center>
                            </div>
                        </div>
                    </div>

                    <div class="row p-4 border rounded">
                        <div class="h3">{{trans('messages.choice_list')}}</div>
                        <div class="col-12" id="choice_options">
                            <div class="p-3 border rounded row mb-2">
                                <span class="position-absolute folat-right text-danger cursor-pointer font-weight-bold close-icon" title="{{trans('messages.remove_this_choice')}}" onclick="removeChoice(this)"><i class="tio-clear"></i></span>
                                
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="">{{__('messages.choice')}} {{__('messages.title')}}</label>
                                        <input type="text" name="choice[0][title]" class="form-control" required>
                                    </div> 
                                    <div class="h5 border_bottom">{{trans('messages.options')}}</div>
                                    <ul class="list-group" id="choice_0">
                                        <li class="list-group-item text-center text-danger">{{trans('messages.no_options_warning')}}</li>
                                    </ul>                                    
                                </div>
                                <div class="col-sm-6 col-12">
                                    <div class="form-group">
                                        <label class="input-label" for="">{{__('messages.select')}} {{__('messages.food')}}</label>
                                        <select class="form-control js-select2-custom choice_item" multiple="multiple" onchange="updateOptions(this, '0')" required>
                                            
                                        </select>
                                    </div>   
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlSelect1">{{__('messages.addon')}}<span
                                                class="input-label-secondary" title="{{__('messages.restaurant_required_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.restaurant_required_warning')}}"></span></label>
                                        <select name="choice[0][addon_ids][]" class="form-control js-select2-custom add_on" multiple="multiple">
        
                                        </select>
                                    </div>                                      
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-right">
                            <button class="btn btn-secondary" type="button" onclick="addChoice()"><i class="tio-add-circle-outlined mr-2"></i>{{trans('messages.add_choice')}}</button>
                        </div>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/tags-input.min.js"></script>
    <script>
        var item_list = null;
        var addon_list = null;
        var choise_count = 1;
        function getRestaurantData(route, class_name) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('.' + class_name).empty().append(data.options);
                    addon_list = data.options;
                },
            });
        }

        function getRequest(route, id, restaurant_id) {
            $.get({
                url: route,
                dataType: 'json',
                success: function (data) {
                    $('.' + id).empty().append(data.options);
                    item_list = data.options;
                },
            });
            getRestaurantData('{{url('/')}}/admin/vendor/get-addons?data[]=0&price=1&restaurant_id='+restaurant_id,'add_on')
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

        function updateOptions(e, tergat) {
            let data = $(e).select2('data');
            let target = $('#choice_'+tergat);
            target.empty();
            if(data.length == 0) {
                target.append('<li class="list-group-item text-center text-danger">{{trans('messages.no_options_warning')}}</li>');
            } else {
                data.forEach((element, index) => {
                    target.append('<li class="list-group-item">'+element.text+'<input type="hidden" name="choice['+tergat+'][items]['+index+'][id]" value="'+element.id+'"><input type="hidden" name="choice['+tergat+'][items]['+index+'][name]" value="'+element.text+'"></li>');
                    console.log('id='+element.id,' text='+element.text, ' index='+index);
                });                
            }
        }

        function addChoice()
        {
            if(!item_list){
                toastr.error('{{trans('messages.please_select_restaurant')}}', Error, {
                    CloseButton: true,
                    ProgressBar: true
                });
                return;
            }
            addon_list = addon_list?addon_list:''
            let text = `<div class="p-3 border rounded row mb-2 position-relative">
                <span class="position-absolute folat-right text-danger cursor-pointer font-weight-bold close-icon" title="{{trans('messages.remove_this_choice')}}" onclick="removeChoice(this)"><i class="tio-clear"></i></span>
                <div class="col-sm-6 col-12">
                    <div class="form-group">
                        <label class="input-label" for="">{{__('messages.choice')}} {{__('messages.title')}}</label>
                        <input type="text" name="choice[`+choise_count+`][title]" class="form-control" required>
                    </div> 
                    <div class="h5 border_bottom">{{trans('messages.options')}}</div>
                    <ul class="list-group" id="choice_`+choise_count+`">
                        <li class="list-group-item text-center text-danger">{{trans('messages.no_options_warning')}}</li>
                    </ul>                                    
                </div>
                <div class="col-sm-6 col-12">
                    <div class="form-group">
                        <label class="input-label" for="">{{__('messages.select')}} {{__('messages.food')}}</label>
                        <select class="form-control js-select2-custom choice_item" multiple="multiple" onchange="updateOptions(this, '`+choise_count+`')" required>
                            `+item_list+`
                        </select>
                    </div>   
                    <div class="form-group">
                        <label class="input-label" for="exampleFormControlSelect1">{{__('messages.addon')}}<span
                                class="input-label-secondary" title="{{__('messages.restaurant_required_warning')}}"><img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.restaurant_required_warning')}}"></span></label>
                        <select name="choice[`+choise_count+`][addon_ids][]" class="form-control js-select2-custom add_on" multiple="multiple">
                            `+addon_list+`
                        </select>
                    </div>                                      
                </div>
            </div>`;

            $('#choice_options').append(text);
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            });
            // $('.js-select2-custom').select2();
            choise_count++;
        }

        function removeChoice(element){
            element.parentNode.remove();
        }

        $("#customFileEg1").change(function () {
            readURL(this);
        });


        $(document).ready(function(){
            $('#date_from').attr('min',(new Date()).toISOString().split('T')[0]);
            $('#date_to').attr('min',(new Date()).toISOString().split('T')[0]);
            $('.js-select2-custom').each(function () {
                var select2 = $.HSCore.components.HSSelect2.init($(this));
            }); 
            $('.js-data-example-ajax').select2({
                ajax: {
                    url: '{{url('/')}}/admin/vendor/get-restaurants',
                    data: function (params) {
                        return {
                            q: params.term, // search term
                            no_zone: 1,
                            page: params.page
                        };
                    },
                    processResults: function (data) {
                        return {
                        results: data
                        };
                    },
                    __port: function (params, success, failure) {
                        var $request = $.ajax(params);

                        $request.then(success);
                        $request.fail(failure);

                        return $request;
                    }
                }
            });
        });

        $('#deal_form').on('submit', function () {
            var formData = new FormData(this);
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.post({
                url: "{{route('admin.deals.store')}}",
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function (data) {
                    if (data.errors) {
                        for (var i = 0; i < data.errors.length; i++) {
                            toastr.error(data.errors[i].message, {
                                CloseButton: true,
                                ProgressBar: true
                            });
                        }
                    } else {
                        toastr.success('Deal created successfully!', {
                            CloseButton: true,
                            ProgressBar: true
                        });
                        setTimeout(function () {
                            location.href = '{{route('admin.deals.index')}}';
                        }, 2000);
                    }
                }
            });
        });
    </script>
@endpush
