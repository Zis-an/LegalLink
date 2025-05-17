@extends('adminlte::page')

@section('title', 'View Consultation')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Consultation - {{ $consultation->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('consultations.index') }}">Consultations</a></li>
                <li class="breadcrumb-item active">View Consultation</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('consultations.show')
                <div class="card">
                    <div class="card-body">
                        @if (count($errors) > 0)
                            <div class = "alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="client_id">Client</label>
                                <select name="client_id" class="form-control select2" disabled>
                                    <option value="{{ $consultation->client_id }}">{{ $consultation->client->user->name }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="lawyer_id">Lawyer</label>
                                <select name="lawyer_id" class="form-control select2" disabled>
                                    <option value="{{ $consultation->lawyer_id }}">{{ $consultation->lawyer->user->name }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="case_id">Case</label>
                                <select name="case_id" class="form-control select2" disabled>
                                    <option value="{{ $consultation->case_id }}">{{ $consultation->case->title }}</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="date_and_time">Date & Time</label>
                                <input type="datetime-local" name="date_and_time" class="form-control"
                                       value="{{ \Carbon\Carbon::parse($consultation->date_and_time)->format('Y-m-d\TH:i') }}" disabled>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="mode">Mode</label>
                                <select name="mode" class="form-control" disabled>
                                    <option value="physical" {{ $consultation->mode == 'physical' ? 'selected' : '' }}>Physical</option>
                                    <option value="virtual" {{ $consultation->mode == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="status">Status</label>
                                <select name="status" class="form-control" disabled>
                                    <option value="Scheduled" {{ $consultation->status == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                    <option value="Completed" {{ $consultation->status == 'Completed' ? 'selected' : '' }}>Completed</option>
                                    <option value="Missed" {{ $consultation->status == 'Missed' ? 'selected' : '' }}>Missed</option>
                                </select>
                            </div>
                        </div>

                        <form action="{{ route('consultations.destroy', $consultation->id) }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')

                            @can('consultations.list')
                                <a href="{{ route('consultations.index') }}" class="btn btn-info btn-sm">Go Back</a>
                            @endcan

                            @can('consultations.update')
                                <a href="{{ route('consultations.edit', $consultation->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-pen"></i> Edit
                                </a>
                            @endcan

                            @can('consultations.delete')
                                <button type="button" onclick="confirmDelete(this)" class="btn btn-danger btn-sm">
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

@section('footer')
@stop

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

@section('plugins.Sweetalert2', true)

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
        function confirmDelete(button) {
            event.preventDefault();
            var row = $(button).closest("tr");
            var form = $(button).closest("form");
            Swal.fire({
                title: @json(__('Delete Consultation')),
                text: @json(__('Are you sure you want to delete this consultation?')),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: @json(__('Delete')),
                cancelButtonText: @json(__('Cancel')),
            }).then((result) => {
                console.log(result)
                if (result.value) {
                    // Trigger the form submission
                    form.submit();
                }
            });
        }
    </script>
@stop
