@extends('adminlte::page')

@section('title', 'Create Proposal')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Create Proposal</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bids.index') }}">Proposals</a></li>
                <li class="breadcrumb-item active">Create Proposal</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('bids.create')
                <div class="card">
                <div class="card-body">
                    <form action="{{ route('bids.store') }}" method="POST">
                        @csrf
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
                            <input type="hidden" name="case_id" value="{{ $case->id }}">

                            @if(auth()->user()->hasRole('lawyer'))
                                <input type="hidden" name="lawyer_id" value="{{ $lawyer->id }}">
                            @elseif(auth()->user()->hasRole('admin'))
                                <div class="form-group col-md-4">
                                    <label for="lawyer_id">Lawyer</label>
                                    <select name="lawyer_id" class="form-control select2" required>
                                        @if($lawyers->isNotEmpty())
                                            @foreach ($lawyers as $lawyer)
                                                <option  {{ old('lawyer_id') == $lawyer->id ? 'selected' : '' }}>
                                                    {{ $lawyer->user->name }}
                                                </option>
                                            @endforeach

                                        @else
                                            <option value="">No lawyers available</option>
                                        @endif
                                    </select>
                                </div>
                            @endif

                            <div class="form-group col-md-4">
                                <label for="fee">Fees</label>
                                <input type="number" name="fee" class="form-control" required value="{{ old('fee') }}" placeholder="Enter fees">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="time_estimated">Estimated Date</label>
                                <input type="date" name="time_estimated" class="form-control" required value="{{ old('time_estimated') }}">
                            </div>
                        </div>

                        @can('bids.create')
                            <button type="submit" class="btn btn-primary">Create</button>
                        @endcan

                        <a href="{{ route('bids.index') }}" class="btn btn-secondary">Cancel</a>
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
