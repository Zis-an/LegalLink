@extends('adminlte::page')

@section('title', 'View Proposal')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Proposal</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bids.index') }}">Proposals</a></li>
                <li class="breadcrumb-item active">View Proposal</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('bids.show')
                <div class="card">
                    <div class="card-body">
                        @if(count($errors) > 0)
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            @if(!auth()->user()->hasRole('lawyer'))
                                <div class="form-group col-md-4">
                                    <label for="lawyer_id">Lawyer</label>
                                    <select name="lawyer_id" class="form-control select2" disabled>
                                        <option value="{{ $bid->lawyer->id }}">{{ $bid->lawyer->user->name }}</option>
                                    </select>
                                </div>
                            @endif

                            <div class="form-group col-md-4">
                                <label for="fee">Fees</label>
                                <input type="number" name="fee" class="form-control" step="0.01" disabled
                                       value="{{ old('fee', $bid->fee) }}" placeholder="Enter fees">
                            </div>

                            <div class="form-group col-md-4">
                                <label for="time_estimated">Estimated Date</label>
                                <input type="date" name="time_estimated" class="form-control" disabled
                                       value="{{ old('time_estimated', $bid->time_estimated) }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="status">Case Status</label>
                                <select name="status" class="form-control" disabled>
                                    <option value="pending" {{ old('status', $bid->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="accepted" {{ old('status', $bid->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="rejected" {{ old('status', $bid->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <div class="form-group col-md-6 my-auto">
                                <a href="{{ url('chatify/' . $bid->lawyer->user->id) }}" class="btn btn-xl px-5 mt-2 btn-success">Chat</a>
                            </div>
                        </div>


                        <form action="{{ route('bids.destroy', $bid->id) }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')

                            @can('bids.list')
                                <a href="{{ route('bids.index') }}" class="btn btn-info btn-sm">Go Back</a>
                            @endcan

                            @can('bids.update')
                                <a href="{{ route('bids.edit', $bid->id) }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-pen"></i> Edit
                                </a>
                            @endcan

                            @can('bids.delete')
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
                title: @json(__('Delete Bid')),
                text: @json(__('Are you sure you want to delete this bid?')),
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
