@extends('layouts.vendor.app')
@section('title',__('messages.restaurant_view'))
@push('css_or_js')
    <!-- Custom styles for this page -->
@endpush

@section('content')
<div class="content container-fluid"> 
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header" >
                <h3 class="mb-0  text-capitalize position-absolute">{{__('messages.my_shop')}} {{__('messages.info')}} </h3>
            </div>
            <div class="card-body">
                @if($shop->cover_photo)
                <div class="row">
                    <div class="col-12"  style="max-height:250px; overflow-y: hidden;">
                         <img src="{{asset('storage/app/public/restaurant/cover/'.$shop->cover_photo)}}" onerror="this.src='{{asset('public/assets/admin/img/900x400/img1.jpg')}}'" style="max-height:auto;width: 100%;">
                    </div>
                </div>
                @endif
                <div class="row mt-2">
                    @if($shop->image=='def.png')
                    <div class="col-md-4">
                        <img height="200" width="200"  class="rounded-circle border"
                        src="{{asset('public/assets/back-end')}}/img/shop.png"
                        onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                        alt="User Pic">
                    </div>
                    
                    @else
                    
                        <div class="col-md-4">
                            <img src="{{asset('storage/app/public/restaurant/'.$shop->logo)}}" class="rounded-circle border"
                            onerror="this.src='{{asset('public/assets/admin/img/160x160/img1.jpg')}}'"
                            height="200" width="200" alt="">
                        </div>

                    
                    @endif
                 
                    <!-- http://localhost/Food-multivendor/public/assets/admin/img/restaurant_cover.jpg -->
                    <div class="col-md-8 mt-4">
                        <span class="h4">{{__('messages.name')}} : {{$shop->name}}</span><br>
                        <span class="h5">{{__('messages.phone')}} : <a style="text-decoration:none; color:black;" href="tel:{{$shop->phone}}">{{$shop->phone}}</a></span><br>
                        <span class="h5">{{__('messages.address')}} : {{$shop->address}}</span><br>
                        <span class="h5">{{__('messages.admin_commission')}} : {{(isset($shop->comission)?$shop->comission:\App\Models\BusinessSetting::where('key','admin_commission')->first()->value)}}%</span><br>
                        <span class="h5">{{__('messages.vat/tax')}} : {{$shop->tax}}%</span><br>
                        <span class="h5">{{__('messages.agreement')}} : 
                            @if($shop->agreement)
                            <a href="{{asset("/storage/app/public/restaurant/agreement/{$shop->agreement}")}}" target="_blank">{{'view agreement'}}</a> 
                            @else
                            {{trans('messages.not_found')}}
                            @endif
                            @if($shop->edit_agreement)
                                <a class="btn btn-sm btn-border" data-toggle="collapse" href="#collapseAggrement" role="button" aria-expanded="false" aria-controls="collapseAggrement"><i class="tio-edit"></i></a>
                            @endif
                        </span><br>
                        
                        <div class="collapse my-2" id="collapseAggrement">
                            <form action="{{route('admin.vendor.update-agreement',['id'=>$shop->id])}}" enctype="multipart/form-data" method="POST" id="agreement_upload">
                                @csrf
                                <div class="input-group">
                                    <div class="custom-file">
                                        <input type="file" class="custom-file-input" name="agreement" id="inputAgreement" accept="application/pdf" required>
                                        <label class="custom-file-label" for="inputAgreement">{{trans('messages.choose_file')}}</label>
                                    </div>
                                    <div class="input-group-append">
                                        <button class="btn btn-outline-secondary" type="button" onclick="form_alert('agreement_upload','{{trans('messages.you_want_to_update_the_agreement')}}');">{{trans('messages.upload')}}</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <a class="btn btn-primary mt-1" href="{{route('vendor.shop.edit')}}">{{trans('messages.edit_restaurant')}}</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@endsection

@push('script_2')
    <script>
        $("#inputAgreement").on("change", function(){
            // Name of file and placeholder
            var file = this.files[0].name;
            var dflt = $(this).attr("placeholder");
            if($(this).val()!=""){
                $("[for=inputAgreement]").html(file);
            } else {
                $("[for=inputAgreement]").html(dflt);
            }
        });
    </script>
@endpush
