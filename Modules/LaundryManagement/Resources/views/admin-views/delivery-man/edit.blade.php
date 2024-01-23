@extends('layouts.admin.app')

@section('title','Update Rider')

@push('css_or_js')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/css/intlTelInput.css" integrity="sha512-gxWow8Mo6q6pLa1XH/CcH8JyiSDEtiwJV78E+D+QP0EVasFs8wKXq16G8CLD4CJ2SnonHr4Lm/yY2fSI2+cbmw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        .iti{
            width:100%;
        }
    </style>
@endpush

@section('content')
    <div class="content container-fluid">
        <!-- Page Header -->
        <div class="page-header">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{__('messages.update')}} {{__('messages.deliveryman')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('admin.laundry.delivery-man.update',[$delivery_man['id']])}}" method="post"
                      enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="row">
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.first')}} {{__('messages.name')}}</label>
                                <input type="text" value="{{$delivery_man['f_name']}}" name="f_name"
                                       class="form-control" placeholder="{{__('messages.first_name')}}"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.last')}} {{__('messages.name')}}</label>
                                <input type="text" value="{{$delivery_man['l_name']}}" name="l_name"
                                       class="form-control" placeholder="{{__('messages.last_name')}}"
                                       required>
                            </div>
                        </div>
                        <div class="col-md-4  col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.email')}}</label>
                                <input type="email" value="{{$delivery_man['email']}}" name="email" class="form-control"
                                       placeholder="Ex : ex@example.com"
                                       required>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-sm-3 col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.father_name')}}</label>
                                <input type="text" name="father_name" value="{{$delivery_man['father_name']}}" name="father_name" class="form-control" placeholder="Father name"
                                       required>
                            </div>
                        </div>
                        <div class="col-sm-3 col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.deliveryman')}} {{__('messages.type')}}</label>
                                <select name="earning" class="form-control" required>
                                    {{-- <option value="1" {{$delivery_man->earning?'selected':''}}>{{__('messages.freelancer')}}</option> --}}
                                    <option value="0" {{$delivery_man->earning?'':'selected'}}>{{__('messages.salary_based')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3 col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.vehicle_type')}}</label>
                                <select name="vehicle_type_id" class="form-control" required data-placeholder="{{__('messages.select')}} {{__('messages.vehicle_type')}}">
                                <option value="" readonly="true" hidden="true">{{__('messages.select')}} {{__('messages.vehicle_type')}}</option>
                                @foreach(Modules\LaundryManagement\Entities\LaundryVehicleType::all() as $type)
                                    <option value="{{$type->id}}" {{$type->id == $delivery_man->vehicle_type_id?'selected':''}}>{{$type->name}}</option>
                                @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-sm-3  col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.zone')}}</label>
                                <select name="zone_id" class="form-control">
                                @foreach(Modules\LaundryManagement\Entities\LaundryZone::all() as $zone)
                                    @if(isset(auth('admin')->user()->zone_id))
                                        @if(auth('admin')->user()->zone_id == $zone->id)
                                            <option value="{{$zone->id}}" {{$zone->id == $delivery_man->zone_id?'selected':''}}>{{$zone->name}}</option>
                                        @endif
                                    @else
                                    <option value="{{$zone->id}}" {{$zone->id == $delivery_man->zone_id?'selected':''}}>{{$zone->name}}</option>
                                    @endif
                                @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                    {{-- @php
                        $document_types = \App\CentralLogics\Helpers::get_business_settings('documents_type')??[];
                    @endphp --}}
                    {{-- @foreach ($document_types as $key=>$document_type) --}}
                    {{-- <div style="border: 1px solid #d6d9e3; border-radius: 5px;" class="mb-3 p-2">
                        <div class="row">
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="input-label" for="exampleFormControlInput1">{{$document_type['document_title']}}</label>
                                    <input type="hidden" name="document[{{$key}}][type]" value="{{$document_type['document_title']}}">
                                    <input type="text" name="document[{{$key}}][data]" class="form-control" value="{{ count($delivery_man->documents)>0 ? $delivery_man->documents[$key]->data : '' }}"
                                           placeholder="Ex : DH-23434-LS"
                                           required>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="form-group">
                                    <label class="input-label" for="document-{{$key}}-file">{{$document_type['document_title']}} {{trans('messages.file')}} ({{trans("messages.{$document_type['document_type']}")}}) <small class="badge badge-soft-danger">{{ trans('Note: Uploading new file will replace previous file.') }}</small></label> --}}
                                    {{-- <input class="form-control custom-file-input" type="file" name="document[{{$key}}][file][]" id="document-{{$key}}-file" multiple accept="{{$document_type['document_type']=='pdf'?'.pdf':'.png'}}"> --}}
                                    {{-- <div class="custom-file">
                                        <input type="hidden" name="document[{{$key}}][file_type]" value="{{$document_type['document_type']}}">
                                        <input type="file" class="custom-file-input" name="document[{{$key}}][doc_file][]" id="document-{{$key}}-file" data-limit="{{$document_type['file_count']}}" @if($document_type['file_count']>1) multiple data-toggle="tooltip" data-placement="top" title="If you want to upload multitple files, please select them together." @endif accept="{{$document_type['document_type']=='pdf'?'.pdf':'.png'}}">
                                        <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                    </div> --}}
                                {{-- </div> --}}
                            {{-- </div> --}}
                        {{-- </div> --}}
                        {{-- @if (count($delivery_man->documents)>0 && $delivery_man->documents[$key]->document_type == 'image')
                        @php($files = $delivery_man->documents[$key]->files)
                            <div class="text-center mb-2 mr-10">
                                @foreach ($files as $file)
                                    <img height="150" style="border: 1px solid; border-radius: 10px;" src="{{asset('storage/app/public/delivery-man').'/'.$file}}">
                                @endforeach
                            </div>
                        @endif
                        @if (count($delivery_man->documents)>0 && $delivery_man->documents[$key]->document_type == 'pdf')
                        @php($files = $delivery_man->documents[$key]->files)
                            <div class="text-center m-2">
                                @foreach ($files as $file)
                                <a class="btn btn-success btn-sm" href="{{asset('storage/app/public/delivery-man').'/'.$file}}" target="_blank">View {{Str::limit($file,15,'...')}}</a>
                                @endforeach
                            </div>
                        @endif --}}
                    {{-- </div> --}}
                    {{-- @endforeach
                         --}}
                    <small class="nav-subtitle text-secondary border-bottom">{{__('messages.login')}} {{__('messages.info')}}</small>
                    <br>
                    <div class="row">
                        <div class="col-sm-6 col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.phone')}}</label>
                                <input type="text" id="phone" name="phone" value="{{$delivery_man['phone']}}" class="form-control"
                                        placeholder="Ex : 017********"
                                        required>
                            </div>
                        </div>
                        <div class="col-sm-6 col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleFormControlInput1">{{__('messages.password')}}</label>
                                <input type="text" name="password" class="form-control" placeholder="Ex : password">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>{{__('messages.deliveryman')}} {{__('messages.image')}}</label><small style="color: red">* ( {{__('messages.ratio')}} 1:1 )</small>
                        <div class="custom-file">
                            <input type="file" name="image" id="customFileEg1" class="custom-file-input"
                                   accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                            <label class="custom-file-label" for="customFileEg1">{{__('messages.choose')}} {{__('messages.file')}}</label>
                        </div>

                        <center class="pt-4">
                            <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                 src="{{asset('storage/app/public/delivery-man').'/'.$delivery_man['image']}}" alt="delivery-man image"/>
                        </center>
                    </div>
                    <hr>
                    <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
<script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput.min.js" integrity="sha512-QMUqEPmhXq1f3DnAVdXvu40C8nbTgxvBGvNruP6RFacy3zWKbNTmx7rdQVVM2gkd2auCWhlPYtcW2tHwzso4SA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/intlTelInput-jquery.min.js" integrity="sha512-hkmipUFWbNGcKnR0nayU95TV/6YhJ7J9YUAkx4WLoIgrVr7w1NYz28YkdNFMtPyPeX1FrQzbfs3gl+y94uZpSw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.min.js" integrity="sha512-lv6g7RcY/5b9GMtFgw1qpTrznYu1U4Fm2z5PfDTG1puaaA+6F+aunX+GlMotukUFkxhDrvli/AgjAu128n2sXw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script> -->
    <link rel="shortcut icon" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/img/flags.png" type="image/x-icon">
    <link rel="shortcut icon" href="https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/img/flags@2x.png" type="image/x-icon">
    <script>
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

        @php($country=\App\Models\BusinessSetting::where('key','country')->first())
        var phone = $("#phone").intlTelInput({
            utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/8.4.6/js/utils.js",
            nationalMode: true,
            autoHideDialCode: true,
            autoPlaceholder: "ON",
            dropdownContainer: document.body,
            formatOnDisplay: true,
            hiddenInput: "phone",
            initialCountry: "{{$country?$country->value:auto}}",
            placeholderNumberType: "MOBILE",
            separateDialCode: true
        });

        $('.custom-file-input').change(function (e) {
            var files = [];
            var limit = $(this).attr('data-limit');
            console.log(limit);
            if($(this)[0].files.length > limit){
                toastr.error("{{trans('messages.maximum_upload_limit')}}"+limit+"{{trans('messages.files')}}", {
                    CloseButton: true,
                    ProgressBar: true
                });
                return false;
            }
            for (var i = 0; i < $(this)[0].files.length; i++) {
                files.push($(this)[0].files[i].name);
            }
            $(this).next('.custom-file-label').html(files.join(', '));
        });
    </script>

    {{-- <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '120px',
                groupClassName: 'col-2',
                maxFileSize: '',
                placeholderImage: {
                    image: '{{asset('public/assets/admin/img/400x400/img2.jpg')}}',
                    width: '100%'
                },
                dropFileLabel: "Drop Here",
                onAddRow: function (index, file) {

                },
                onRenderedPreview: function (index) {

                },
                onRemoveRow: function (index) {

                },
                onExtensionErr: function (index, file) {
                    toastr.error('Please only input png or jpg type file', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('File size too big', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script> --}}
@endpush
