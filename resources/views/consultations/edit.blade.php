@extends('adminlte::page')

@section('title', 'Edit Consultation')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Consultation - {{ $consultation->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('consultations.index') }}">Consultations</a></li>
                <li class="breadcrumb-item active">Edit Consultation</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('consultations.update')
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('consultations.update', $consultation->id) }}" method="POST">
                            @csrf
                            @method('PUT')

                            @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <div class="row">
                                <div class="form-group col-md-3">
                                    <label for="client_id">Client</label>
                                    <select name="client_id" class="form-control select2" required>
                                        <option value="">Select client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id', $consultation->client_id) == $client->id ? 'selected' : '' }}>
                                                {{ $client->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="lawyer_id">Lawyer</label>
                                    <select name="lawyer_id" class="form-control select2" required>
                                        <option value="">Select lawyer</option>
                                        @foreach($lawyers as $lawyer)
                                            <option value="{{ $lawyer->id }}" {{ old('lawyer_id', $consultation->lawyer_id) == $lawyer->id ? 'selected' : '' }}>
                                                {{ $lawyer->user->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="case_id">Case</label>
                                    <select name="case_id" class="form-control select2" required>
                                        <option value="">Select case</option>
                                        @foreach($cases as $case)
                                            <option value="{{ $case->id }}" {{ old('case_id', $consultation->case_id) == $case->id ? 'selected' : '' }}>
                                                {{ $case->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="">Select status</option>
                                        <option value="Scheduled" {{ old('status', $consultation->status) == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="Completed" {{ old('status', $consultation->status) == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="Missed" {{ old('status', $consultation->status) == 'Missed' ? 'selected' : '' }}>Missed</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="date_and_time">Date & Time</label>
                                    <input type="datetime-local" name="date_and_time" class="form-control"
                                           value="{{ old('date_and_time', \Carbon\Carbon::parse($consultation->date_and_time)->format('Y-m-d\TH:i')) }}" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="mode">Mode</label>
                                    <select name="mode" class="form-control" required>
                                        <option value="">Select mode</option>
                                        <option value="physical" {{ old('mode', $consultation->mode) == 'physical' ? 'selected' : '' }}>Physical</option>
                                        <option value="virtual" {{ old('mode', $consultation->mode) == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    </select>
                                </div>

                            </div>

                            @can('consultations.update')
                                <button type="submit" class="btn btn-primary">Update</button>
                            @endcan

                            <a href="{{ route('consultations.index') }}" class="btn btn-secondary">Cancel</a>
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
@stop
