@extends('adminlte::page')

@section('title', 'Edit Issue')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Issue</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cases.index') }}">Issues</a></li>
                <li class="breadcrumb-item active">Edit Issue</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('cases.list')
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('cases.update', $case->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="form-group col-md-4 d-none">
                                    <label for="client_id">Client</label>
                                    <select name="client_id" class="form-control select2" required>
                                        @foreach ($clients as $client)
                                            <option value="{{ $client->id }}" {{ $case->client_id == $client->id ? 'selected' : '' }}>
                                                {{ $client->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4 d-none">
                                    <label for="title">Case Title</label>
                                    <input type="text" name="title" class="form-control" value="{{ old('title', $case->title) }}" required>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="country">Country</label>
                                    <input type="text" name="country" id="country" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="division">Division</label>
                                    <input type="text" name="division" id="division" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="district">District</label>
                                    <input type="text" name="district" id="district" class="form-control">
                                </div>
                                <div class="form-group col-md-3">
                                    <label for="thana">Police Station</label>
                                    <input type="text" name="thana" id="thana" class="form-control">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="category">Issue Category</label>
                                    <select name="category" id="category" class="form-control" required>
                                        <option value="">-- Select Category --</option>
                                        <option value="civil" {{ old('category', $lawsuit->category ?? '') == 'civil' ? 'selected' : '' }}>Civil</option>
                                        <option value="criminal" {{ old('category', $lawsuit->category ?? '') == 'criminal' ? 'selected' : '' }}>Criminal</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="status">Issue's Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="open" {{ $case->status == 'open' ? 'selected' : '' }}>Open</option>
                                        <option value="in_progress" {{ $case->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                        <option value="closed" {{ $case->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="uploaded_file">Upload File (jpg, png, mp3, mp4, max 100MB)</label>
                                    <input type="file" name="uploaded_file" id="uploaded_file" class="form-control" accept=".jpg,.jpeg,.png,.mp3,.mp4" onchange="validateFileSize(this)">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="description">Issue's Description</label>
                                <textarea name="description" rows="4" class="form-control" required>{{ old('description', $case->description) }}</textarea>
                            </div>

                            @can('cases.update')
                                <button type="submit" class="btn btn-success">Update Issue</button>
                            @endcan

                            <a href="{{ route('cases.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 37px; /* Set to your desired height */
            user-select: none;
            -webkit-user-select: none;
        }

        /* Adjust the line-height of the rendered text inside Select2 */
        .select2-container--classic .select2-selection--single .select2-selection__rendered {
            line-height: 34px; /* Adjust line height */
        }

        /* Style the Select2 arrow and set its height */
        .select2-container--classic .select2-selection--single .select2-selection__arrow {
            background-color: #ddd;
            border: none;
            border-left: 1px solid #aaa;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            height: 35px; /* Set height for the arrow */
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
            background-image: -webkit-linear-gradient(top, #eeeeee 50%, #cccccc 100%);
            background-image: -o-linear-gradient(top, #eeeeee 50%, #cccccc 100%);
            background-image: linear-gradient(to bottom, #eeeeee 50%, #cccccc 100%);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFEEEEEE', endColorstr='#FFCCCCCC', GradientType=0);
        }


        /* Custom style for Toastr notifications */
        .toast-info .toast-message {
            display: flex;
            align-items: center;
        }
        .toast-info .toast-message i {
            margin-right: 10px;
        }
        .toast-info .toast-message .notification-content {
            display: flex;
            flex-direction: row;
            align-items: center;
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
    <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script>
    <script>
        Pusher.logToConsole = true;

        // Initialize Pusher
        var pusher = new Pusher('f5d4f2a1ed3a59340e6a', {
            cluster: 'mt1'
        });

        // Subscribe to the channel
        var channel = pusher.subscribe('notification');

        // Bind to the event
        channel.bind('test.notification', function(data) {
            console.log('Received data:', data); // Log full data object

            if (data.data && data.data.author && data.data.category) {
                toastr.info(
                    `<div class="notification-content">
                <i class="fas fa-user"></i> <span>   ${data.data.author}</span>
                <i class="fas fa-book" style="margin-left: 20px;"></i> <span>  ${data.data.category}</span>
            </div>`,
                    'New Issue Notification',
                    {
                        closeButton: true,
                        progressBar: true,
                        timeOut: 0,
                        extendedTimeOut: 0,
                        positionClass: 'toast-top-right',
                        enableHtml: true
                    }
                );
            } else {
                console.error('Invalid data received:', data);
            }
        });

        // Debugging line
        pusher.connection.bind('connected', function() {
            console.log('Pusher connected');
        });
    </script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
    <script>
        function validateFileSize(input) {
            if (input.files[0].size > 100 * 1024 * 1024) {
                alert("File size must be less than 100MB.");
                input.value = ""; // clear the input
            }
        }
    </script>
    <script>
        // const subcategories = {
        //     Civil: [
        //         'Property Disputes',
        //         'Contract Disputes',
        //         'Personal Injury',
        //         'Financial Disputes',
        //         'Family Law',
        //         'Administrative Suits'
        //     ],
        //     Criminal: [
        //         'Crimes Against Humanity',
        //         'Crimes Against Property',
        //         'Crimes Against Persons',
        //         'Crimes Related to Narcotics',
        //         'Other Crimes'
        //     ]
        // };

        // function populateSubcategories(category, selected = null) {
        //     const subcategorySelect = document.getElementById('subcategory');
        //     subcategorySelect.innerHTML = '<option value="">-- Select Subcategory --</option>';
        //
        //     if (subcategories[category]) {
        //         subcategories[category].forEach(sub => {
        //             const opt = document.createElement('option');
        //             opt.value = sub;
        //             opt.text = sub;
        //             if (sub === selected) opt.selected = true;
        //             subcategorySelect.appendChild(opt);
        //         });
        //     }
        // }

        // document.getElementById('category').addEventListener('change', function () {
        //     populateSubcategories(this.value);
        // });

        // Auto-populate on edit page
        {{--document.addEventListener('DOMContentLoaded', function () {--}}
        {{--    const selectedCategory = document.getElementById('category').value;--}}
        {{--    const selectedSub = "{{ old('subcategory', $lawsuit->subcategory ?? '') }}";--}}
        {{--    if (selectedCategory) {--}}
        {{--        populateSubcategories(selectedCategory, selectedSub);--}}
        {{--    }--}}
        {{--});--}}
    </script>
@stop
