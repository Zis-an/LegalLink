@extends('adminlte::page')

@section('title', 'View Lawyer')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Lawyer - {{ $lawyer->user->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lawyers.index') }}">Lawyers</a></li>
                <li class="breadcrumb-item active">View Lawyer</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('lawyers.show')
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Name</label>
                                <input type="text" class="form-control" value="{{ $lawyer->user->name }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="text" class="form-control" value="{{ $lawyer->user->email }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Bar ID</label>
                                <input type="text" class="form-control" value="{{ $lawyer->bar_id }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Practice Area</label>
                                <input type="text" class="form-control" value="{{ $lawyer->practice_area }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Practice Court Name</label>
                                <input type="text" class="form-control" value="{{ $lawyer->practice_court }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Chamber Name</label>
                                <input type="text" class="form-control" value="{{ $lawyer->chamber_name }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Chamber Address</label>
                                <textarea class="form-control" rows="2" disabled>{{ $lawyer->chamber_address }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Photo</label><br>
                                @if ($lawyer->photo)
                                    <img src="{{ asset('storage/' . $lawyer->photo) }}" class="img-thumbnail" width="120">
                                @else
                                    <p class="text-muted">No photo</p>
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('lawyers.destroy', $lawyer->id) }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')

                            @can('lawyers.list')
                                <a href="{{ route('lawyers.index') }}" class="btn btn-info btn-sm">Go Back</a>
                            @endcan

                            @can('lawyers.update')
                                <a href="{{ route('users.edit', $lawyer->user_id) }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-pen"></i> Edit
                                </a>
                            @endcan

                            @can('lawyers.delete')
                                <button type="button" onclick="isDelete(this)" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            @endcan
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@stop

@section('plugins.Sweetalert2', true)

@section('css')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
    <style>
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
        function isDelete(button) {
            event.preventDefault();
            var form = $(button).closest("form");
            Swal.fire({
                title: @json(__('Delete Lawyer')),
                text: @json(__('Are you sure you want to delete this?')),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: @json(__('Delete')),
                cancelButtonText: @json(__('Cancel')),
            }).then((result) => {
                if (result.value) {
                    form.submit();
                }
            });
        }
    </script>
@stop
