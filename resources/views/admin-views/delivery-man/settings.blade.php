@extends('layouts.admin.app')

@section('title',__('messages.delivery_man_settings'))

@push('css_or_js')
    <!-- Custom styles for this page -->
    <link href="{{asset('public/assets/admin/css/croppie.css')}}" rel="stylesheet">
    <style>
        .flex-item{
            padding: 10px;
            flex: 20%;
        }

        /* Responsive layout - makes a one column-layout instead of a two-column layout */
        @media (max-width: 768px) {
            .flex-item{
                flex: 50%;
            }
        }

        @media (max-width: 480px) {
            .flex-item{
                flex: 100%;
            }
        }
    </style>
@endpush

@section('content')
<div class="content container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">{{__('messages.dashboard')}}</a></li>
            <li class="breadcrumb-item">{{__('messages.Delivery Man')}}</li>
            <li class="breadcrumb-item" aria-current="page">{{__('messages.documents')}}</li>
        </ol>
    </nav>

    <!-- Page Header -->
    <div class="page-header">
        <h1 class="page-header-title">{{__('messages.rider_documents')}}</h1>
    </div>
    <!-- End Page Header -->

    <!-- Page Heading -->
    <div class="card my-2">
        <div class="card-body">
            <form action="{{route('admin.delivery-man.settings')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row">
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label class="input-label" for="document_title">{{__('messages.document_title')}}</label>
                            <input type="text" id="document_title"  name="document_title" class="form-control" >
                        </div>                        
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <div class="form-group">
                            <label class="input-label" for="document_type">{{__('messages.input_type')}}</label>
                            <select name="document_type" id="document_type" class="form-control">
                                <option value="pdf">{{__('messages.pdf')}}</option>
                                <option value="image">{{__('messages.image')}}</option>
                            </select>
                        </div> 
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <label class="input-label" for="number_of_files">{{__('messages.number_of_files')}}</label>
                        <input type="number" min="1" max="10" id="number_of_files"  name="number_of_files" class="form-control" >
                    </div>
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success" value="{{__('messages.submit')}}">
                </div>
            </form>
            <div class="col-12">
                <table class="table table-borderless table-thead-bordered table-nowrap table-align-middle card-table">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">{{__('messages.document_title')}}</th>
                            <th scope="col">{{__('messages.input_type')}}</th>
                            <th scope="col">{{__('messages.number_of_files')}}</th>
                            <th scope="col">{{__('messages.actions')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if($documents)
                            @foreach ($documents as $key=>$document)
                                <tr>
                                    <th scope="row">{{$key + 1}}</th>
                                    <td>{{$document['document_title']}}</td>
                                    <td>{{trans("messages.{$document['document_type']}")}}</td>
                                    <td>{{$document['file_count']}}</td>
                                    <td>
                                        <a class="btn btn-sm btn-white" href="javascript:"
                                            onclick="form_alert('sp-{{$key}}','{{__('messages.Want_to_delete_this_item')}}')" title="{{__('messages.delete')}}"><i class="tio-delete-outlined"></i>
                                        </a>
                                        <form action="{{route('admin.delivery-man.document.distroy',['key'=>$key])}}"
                                                method="post" id="sp-{{$key}}">
                                            @csrf @method('delete')
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</div>
@endsection

@push('script_2')
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
            $('#image-viewer-section').show(1000);
        });

        $(document).on('ready', function () {
            
        });
    </script>
@endpush
