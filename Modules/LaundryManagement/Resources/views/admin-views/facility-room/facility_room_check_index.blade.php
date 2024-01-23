@extends('layouts.admin.app')

@section('title',trans('messages.product_arrival_status_in_facility_room'))

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
                    <h1 class="page-header-title"><i class="tio-edit"></i> {{__('messages.package_arrival_status_in_facility_room')}}</h1>
                </div>
            </div>
        </div>
        <!-- End Page Header -->
        <div class="row gx-2 gx-lg-3">
            <div class="col-sm-12 col-lg-12 mb-3 mb-lg-2">
                <div class="card mx-auto">
                    <div class="card-body">
                        <form id="barcode-form" action="javascript:" method="post">
                            @csrf

                            <div class="row">
                                <div class="col-md-4 offset-md-4">
                                    <div class="form-group">
                                        <label class="input-label" for="exampleFormControlInput1">{{__('messages.enter_package_barcode')}}</label>
                                        <input type="text" name="bar_code" class="form-control" placeholder="{{__('messages.barcode')}}" required>
                                    </div>
                                    {{-- <button type="submit" class="btn btn-block btn-primary">{{__('messages.update')}}</button> --}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div id="order-summary">

                </div>
            </div>
        </div>
    </div>

@endsection


@push('script_2')
     <script>

    document.getElementById('barcode-form').addEventListener('input', function (event) {
        var barcodeInput = document.getElementsByName('bar_code')[0];
        var barcode = barcodeInput.value;

        // Check if the barcode has a minimum length of 6 characters
        if (barcode.length >= 6) {
            // Send barcode to the server using AJAX
            $.ajax({
                    url: '{{ route('admin.laundry.in-house-tracking.facility-room-check') }}',
                    method: 'POST',
                    data: {
                        bar_code: barcode
                    },
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    success: function (data)  {
                    if (data.success) {
                        $('#order-summary').empty().html(data.view);
                        toastr.success(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    } else {
                        $('#order-summary').empty().html("");
                        toastr.error(data.message, {
                            CloseButton: true,
                            ProgressBar: true
                        });
                    }
                    // Reset the input field after AJAX call completes
                    barcodeInput.value = '';
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    // Reset the input field after AJAX call completes (even on error)
                    barcodeInput.value = '';
                }
            });
        } else {
                // Barcode does not meet the minimum length requirement, do something (e.g., display an error message)
                // For example, you can show an error message on the page:
                // var errorElement = document.getElementById('barcode-error-message');
                // errorElement.textContent = 'Barcode must have a minimum length of 6 characters.';
        }
    });

    </script>
@endpush
