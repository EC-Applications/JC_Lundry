@extends('layouts.landing.app')

@section('title',__('messages.restaurant_registration'))

@push('css_or_js')
<link rel="stylesheet" href="{{asset('public/assets/admin')}}/css/toastr.css">
<style>
    #map{
        height: 100%;
    }
    @media only screen and (max-width: 768px) {
        /* For mobile phones: */
        #map{
            height: 200px;
        }
    }
</style>
@endpush

@section('content')
    <div class="content container">
        <!-- Page Header -->
        <div class="page-header" style="border-bottom:0;padding-bottom:0;">
            <div class="row align-items-center">
                <div class="col-sm mb-2 mb-sm-0">
                    <h1 class="page-header-title text-center"><i class="tio-add-circle-outlined"></i> {{__('messages.restaurant_application')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <form action="{{route('restaurant.store')}}" method="post" enctype="multipart/form-data" class="js-validate">
                    @csrf

                    <small class="nav-subtitle text-secondary border-bottom">{{__('messages.restaurant')}} {{__('messages.info')}}</small>
                    <br>
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="input-label" for="name">{{__('messages.restaurant')}} {{__('messages.name')}}</label>
                                <input type="text" name="name" class="form-control" placeholder="{{__('messages.first')}} {{__('messages.name')}}" value="{{old('name')}}" required>
                            </div>
                            <div class="form-group">
                                <label class="input-label" for="address">{{__('messages.restaurant')}} {{__('messages.address')}}</label>
                                <textarea type="text" name="address" class="form-control" placeholder="{{__('messages.restaurant')}} {{__('messages.address')}}" required >{{old('address')}}</textarea>
                            </div>
                            <div class="form-group">
                                <label class="input-label" for="tax">{{__('messages.vat/tax')}} (%)</label>
                                <input type="number" name="tax" class="form-control" placeholder="{{__('messages.vat/tax')}}" min="0" step=".01" required value="{{old('tax')}}">
                            </div>
                            <div class="row">
                                <div class="form-group col-md-6 pr-md-1 px-0">
                                    <label class="input-label" for="minimum_delivery_time">{{__('messages.minimum_delivery_time')}}</label>
                                    <input type="number" min="0" step="1" name="minimum_delivery_time" class="form-control" placeholder="30" pattern="^[0-9]{2}$" required value="{{old('minimum_delivery_time')}}">
                                </div>
                                <div class="form-group col-md-6 px-0">
                                    <label class="input-label" for="maximum_delivery_time">{{__('messages.maximum_delivery_time')}}</label>
                                    <input type="number" min="0" step="1" name="maximum_delivery_time" class="form-control" placeholder="40" pattern="[0-9]{2}" required value="{{old('maximum_delivery_time')}}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="input-label">{{__('messages.restaurant')}} {{__('messages.logo')}}<small style="color: red"> ( {{__('messages.ratio')}} 1:1 )</small></label>
                                <div class="custom-file">
                                    <input type="file" name="logo" id="customFileEg1" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*" required>
                                    <label class="custom-file-label" for="logo">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-12" style="margin-top: auto;margin-bottom: auto;">
                            <div class="form-group" style="margin-bottom:0%;">
                                <center>
                                    <img style="height: 200px;border: 1px solid; border-radius: 10px;" id="viewer"
                                        src="{{asset('public/assets/admin/img/400x400/img2.jpg')}}" alt="delivery-man image"/>
                                </center>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="choice_zones">{{__('messages.zone')}}<span
                                        class="input-label-secondary mr-2" title="{{__('messages.select_zone_for_map')}}">
                                        <img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.select_zone_for_map')}}"></span></label>
                                <select name="zone_id" id="choice_zones" required
                                        class="form-control js-select2-custom"  data-placeholder="{{__('messages.select')}} {{__('messages.zone')}}">
                                        <option value="" selected disabled>{{__('messages.select')}} {{__('messages.zone')}}</option>
                                    @foreach(\App\Models\Zone::all() as $zone)
                                        @if(isset(auth('admin')->user()->zone_id))
                                            @if(auth('admin')->user()->zone_id == $zone->id)
                                                <option value="{{$zone->id}}" selected>{{$zone->name}}</option>
                                            @endif
                                        @else
                                        <option value="{{$zone->id}}">{{$zone->name}}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="input-label" for="latitude">{{__('messages.latitude')}}<span
                                        class="input-label-secondary mr-2" title="{{__('messages.restaurant_lat_lng_warning')}}">
                                        <img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.restaurant_lat_lng_warning')}}"></span></label>
                                <input type="number" id="latitude" step="any" min="-90" max="90"
                                       name="latitude" class="form-control"
                                       placeholder="Ex : -84.22213" value="{{old('latitude')}}" required>
                            </div>
                            <div class="form-group">
                                <label class="input-label" for="longitude">{{__('messages.longitude')}}<span
                                        class="input-label-secondary mr-2" title="{{__('messages.restaurant_lat_lng_warning')}}">
                                        <img src="{{asset('/public/assets/admin/img/info-circle.svg')}}" alt="{{__('messages.restaurant_lat_lng_warning')}}"></span></label>
                                <input type="number"
                                       name="longitude" class="form-control" step="any" min="-180" max="180"
                                       placeholder="Ex : 103.344322" id="longitude" value="{{old('longitude')}}" required>
                            </div>
                        </div>

                        <div class="col-md-8">
                            <input id="pac-input" class="controls rounded" style="height: 3em; width: 40%; position: absolute; top: 0px; left: 173px;" title="{{__('messages.search_your_location_here')}}" type="text" placeholder="{{__('messages.search_here')}}"/>
                            <div id="map"></div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <div class="form-group">
                                <label for="name">{{__('messages.upload')}} {{__('messages.cover')}} {{__('messages.photo')}} <span class="text-danger">({{__('messages.ratio')}} 2:1)</span></label>
                                <div class="custom-file">
                                    <input type="file" name="cover_photo" id="coverImageUpload" class="custom-file-input"
                                        accept=".jpg, .png, .jpeg, .gif, .bmp, .tif, .tiff|image/*">
                                    <label class="custom-file-label" for="customFileUpload">{{__('messages.choose')}} {{__('messages.file')}}</label>
                                </div>
                            </div>
                            <center>
                                <img style="max-width: 100%;border: 1px solid; border-radius: 10px; max-height:200px;" id="coverImageViewer"
                                src="{{asset('public/assets/admin/img/900x400/img1.jpg')}}" alt="Product thumbnail"/>
                            </center>
                        </div>
                    </div>

                    <br>
                    <small class="nav-subtitle text-secondary border-bottom">{{__('messages.owner')}} {{__('messages.info')}}</small>
                    <br>
                    <div class="row">
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="input-label" for="f_name">{{__('messages.first')}} {{__('messages.name')}}</label>
                                <input type="text" name="f_name" class="form-control" placeholder="{{__('messages.first')}} {{__('messages.name')}}"
                                     value="{{old('f_name')}}"  required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="input-label" for="l_name">{{__('messages.last')}} {{__('messages.name')}}</label>
                                <input type="text" name="l_name" class="form-control" placeholder="{{__('messages.last')}} {{__('messages.name')}}"
                                value="{{old('l_name')}}"  required>
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="input-label" for="phone">{{__('messages.phone')}}</label>
                                <input type="tel" name="phone" class="form-control" placeholder="Ex : 017********"
                                value="{{old('phone')}}"  oninput="this.value = this.value.replace(/[^+.0-9.-]/g, '').replace(/(\..*)\./g, '$1');"  required maxlength="20" minlength="10">
                            </div>
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="form-group">
                                <label class="input-label" for="phone2">{{__('messages.Alternate_Number')}}</label>
                                <input type="tel" name="phone2" class="form-control" placeholder="Ex : 017********"
                                value="{{old('phone2')}}"  oninput="this.value = this.value.replace(/[^+.0-9.-]/g, '').replace(/(\..*)\./g, '$1');"  maxlength="20" minlength="10">
                            </div>
                        </div>
                    </div>
                    <br>

                    <small class="nav-subtitle text-secondary border-bottom text-capitalize">{{__('messages.login')}} {{__('messages.info')}}</small>
                    <br>
                    <div class="row">
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="email">{{__('messages.email')}}</label>
                                <input type="email" name="email" class="form-control" placeholder="Ex : ex@example.com"
                                value="{{old('email')}}"  required>
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="form-group">
                                <label class="input-label" for="exampleInputPassword">{{__('messages.password')}}</label>
                                <input type="password" name="password" placeholder="{{__('messages.password_length_placeholder',['length'=>'6+'])}}" class="form-control form-control-user" minlength="6" id="exampleInputPassword" required value="{{old('password')}}">
                            </div>
                        </div>
                        <div class="col-md-4 col-12">
                            <div class="js-form-message form-group">
                                <label class="input-label" for="signupSrConfirmPassword">{{__('messages.confirm_password')}}</label>
                                <input type="password" name="confirm-password" class="form-control form-control-user" minlength="6" id="exampleRepeatPassword" placeholder="{{__('messages.password_length_placeholder',['length'=>'6+'])}}" required value="{{old('confirm-password')}}">
                                <div class="pass invalid-feedback">{{__('messages.password_not_matched')}}</div>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">{{__('messages.submit')}}</button>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script_2')
    <script src="{{asset('public/assets/admin')}}/js/toastr.js"></script>
    {!! Toastr::message() !!}

    @if ($errors->any())
        <script>
            @foreach($errors->all() as $error)
            toastr.error('{{$error}}', Error, {
                CloseButton: true,
                ProgressBar: true
            });
            @endforeach
        </script>
    @endif
     <script>
        $('#exampleInputPassword ,#exampleRepeatPassword').on('keyup',function () {
            var pass = $("#exampleInputPassword").val();
            var passRepeat = $("#exampleRepeatPassword").val();
            if (pass==passRepeat){
                $('.pass').hide();
            }
            else{
                $('.pass').show();
            }
        });


        function readURL(input, viewer) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#'+viewer).attr('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#customFileEg1").change(function () {
            readURL(this, 'viewer');
        });

        $("#coverImageUpload").change(function () {
            readURL(this, 'coverImageViewer');
        });
    </script>

    <script src="{{asset('public/assets/admin/js/spartan-multi-image-picker.js')}}"></script>
    <script type="text/javascript">
        $(function () {
            $("#coba").spartanMultiImagePicker({
                fieldName: 'identity_image[]',
                maxCount: 5,
                rowHeight: '120px',
                groupClassName: 'col-lg-2 col-md-4 col-sm-4 col-6',
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
                    toastr.error('{{__('messages.please_only_input_png_or_jpg_type_file')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                },
                onSizeErr: function (index, file) {
                    toastr.error('{{__('messages.file_size_too_big')}}', {
                        CloseButton: true,
                        ProgressBar: true
                    });
                }
            });
        });
    </script>
          <script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
          <script
                  src="https://maps.googleapis.com/maps/api/js?key={{ \App\Models\BusinessSetting::where('key', 'map_api_key')->first()->value }}&libraries=drawing,places&v=3.45.8">
          </script>
          <script>
                @php($default_location = \App\Models\BusinessSetting::where('key', 'default_location')->first())
                @php($default_location = $default_location->value ? json_decode($default_location->value, true) : 0)
                let myLatlng = {
                    lat: {{ $default_location ? $default_location['lat'] : '23.757989' }},
                    lng: {{ $default_location ? $default_location['lng'] : '90.360587' }}
                };
                let map = new google.maps.Map(document.getElementById("map"), {
                    zoom: 13,
                    center: myLatlng,
                });
                var zonePolygon = null;
                let infoWindow = new google.maps.InfoWindow({
                    content: "Click the map to get Lat/Lng!",
                    position: myLatlng,
                });
                var bounds = new google.maps.LatLngBounds();

                function initMap() {
                    // Create the initial InfoWindow.
                    infoWindow.open(map);
                    //get current location block
                    infoWindow = new google.maps.InfoWindow();
                    // Try HTML5 geolocation.
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                myLatlng = {
                                    lat: position.coords.latitude,
                                    lng: position.coords.longitude,
                                };
                                infoWindow.setPosition(myLatlng);
                                infoWindow.setContent("Location found.");
                                infoWindow.open(map);
                                map.setCenter(myLatlng);
                            },
                            () => {
                                handleLocationError(true, infoWindow, map.getCenter());
                            }
                        );
                    } else {
                        // Browser doesn't support Geolocation
                        handleLocationError(false, infoWindow, map.getCenter());
                    }
                    //-----end block------
                    // Create the search box and link it to the UI element.
                    const input = document.getElementById("pac-input");
                    const searchBox = new google.maps.places.SearchBox(input);
                    map.controls[google.maps.ControlPosition.TOP_CENTER].push(input);
                    let markers = [];
                    searchBox.addListener("places_changed", () => {
                        const places = searchBox.getPlaces();

                        if (places.length == 0) {
                        return;
                        }
                        // Clear out the old markers.
                        markers.forEach((marker) => {
                        marker.setMap(null);
                        });
                        markers = [];
                        // For each place, get the icon, name and location.
                        const bounds = new google.maps.LatLngBounds();
                        places.forEach((place) => {
                        if (!place.geometry || !place.geometry.location) {
                            console.log("Returned place contains no geometry");
                            return;
                        }
                        const icon = {
                            url: place.icon,
                            size: new google.maps.Size(71, 71),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(25, 25),
                        };
                        // Create a marker for each place.
                        markers.push(
                            new google.maps.Marker({
                            map,
                            icon,
                            title: place.name,
                            position: place.geometry.location,
                            })
                        );

                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }
                        });
                        map.fitBounds(bounds);
                    });
                }
                initMap();

                function handleLocationError(browserHasGeolocation, infoWindow, pos) {
                    infoWindow.setPosition(pos);
                    infoWindow.setContent(
                        browserHasGeolocation ?
                        "Error: The Geolocation service failed." :
                        "Error: Your browser doesn't support geolocation."
                    );
                    infoWindow.open(map);
                }
                $('#choice_zones').on('change', function() {
                    var id = $(this).val();
                    $.get({
                        url: '{{ url('/') }}/admin/zone/get-coordinates/' + id,
                        dataType: 'json',
                        success: function(data) {
                            if (zonePolygon) {
                                zonePolygon.setMap(null);
                            }
                            zonePolygon = new google.maps.Polygon({
                                paths: data.coordinates,
                                strokeColor: "#FF0000",
                                strokeOpacity: 0.8,
                                strokeWeight: 2,
                                fillColor: 'white',
                                fillOpacity: 0,
                            });
                            zonePolygon.setMap(map);
                            zonePolygon.getPaths().forEach(function(path) {
                                path.forEach(function(latlng) {
                                    bounds.extend(latlng);
                                    map.fitBounds(bounds);
                                });
                            });
                            map.setCenter(data.center);
                            google.maps.event.addListener(zonePolygon, 'click', function(mapsMouseEvent) {
                                infoWindow.close();
                                // Create a new InfoWindow.
                                infoWindow = new google.maps.InfoWindow({
                                    position: mapsMouseEvent.latLng,
                                    content: JSON.stringify(mapsMouseEvent.latLng.toJSON(),
                                        null, 2),
                                });
                                var coordinates = JSON.stringify(mapsMouseEvent.latLng.toJSON(), null,
                                    2);
                                var coordinates = JSON.parse(coordinates);

                                document.getElementById('latitude').value = coordinates['lat'];
                                document.getElementById('longitude').value = coordinates['lng'];
                                infoWindow.open(map);
                            });
                        },
                    });

                });
        document.addEventListener('keypress', function (e) {
            if (e.keyCode === 13 || e.which === 13) {
                e.preventDefault();
                return false;
            }
        });
          </script>
@endpush
