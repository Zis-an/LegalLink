@extends('adminlte::page')

@section('title', 'View Issue')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Issue @if(!empty($case->title)) - {{ $case->title }} @endif</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cases.index') }}">Issues</a></li>
                <li class="breadcrumb-item active">View Issue</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('cases.show')
                <div class="card">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4 d-none">
                                <label for="client_id">Client</label>
                                <select name="client_id" class="form-control select2" disabled>
                                    <option>{{ $case->client->user->name }}</option>
                                </select>
                            </div>

                            @if(!empty(old('title', $case->title)))
                                <div class="form-group col-md-4">
                                    <label for="title">Issue's Title</label>
                                    <input type="text" name="title" class="form-control"
                                           value="{{ old('title', $case->title) }}" disabled>
                                </div>
                            @endif

                            <div class="form-group col-md-4">
                                <label for="status">Case Status</label>
                                <select name="status" class="form-control" disabled>
                                    <option value="open" {{ $case->status == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $case->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="closed" {{ $case->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="row my-3">
                            <div class="form-group col-6">
                                <strong>Category:</strong> {{ $case->category }}
                            </div>
                            @if(!empty($case->subcategory))
                                <div class="form-group col-6">
                                    <strong>Subcategory:</strong> {{ $case->subcategory }}
                                </div>
                            @endif
                        </div>

                        <div class="form-group">
                            <label for="description">Issue's Description</label>
                            <textarea name="description" rows="4" class="form-control" disabled>{{ old('description', $case->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="voice_note">Voice Note</label><br>
                            @if ($case->voice_note)
                                <audio controls>
                                    <source src="{{ asset('storage/' . $case->voice_note) }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                                <br>
                            @endif
                        </div>

                        @if(auth()->user()->hasRole(['client', 'admin']))
                            @if($case->bids->count())
                                <table id="bidsList" class="table table-bordered my-4">
                                    <thead>
                                    <tr>
                                        <th>Lawyer Name</th>
                                        <th>Fee</th>
                                        <th>Time Estimated</th>
                                        <th>Chat</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($case->bids as $bid)
                                        <tr>
                                            <td>{{ $bid->lawyer->user->name }}</td>
                                            <td>{{ $bid->fee }}</td>
                                            <td>{{ $bid->time_estimated }}</td>
                                            <td>
                                                <a href="{{ url('chatify/' . $bid->lawyer->user->id) }}" class="btn btn-sm btn-success">
                                                    Chat
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            @else
                                <p>No bids yet.</p>
                            @endif
                        @endif

                        @if(auth()->user()->hasRole(['lawyer', 'admin']))
                            <a href="{{ url('chatify') }}" class="btn btn-success my-3">
                                Go to Chat Inbox
                            </a>
                        @endif

                        <div class="form-group mt-3">
                        @can('cases.list')
                            <a href="{{ route('cases.index') }}" class="btn btn-primary">Go Back</a>
                        @endcan

                        @can('cases.update')
                            <a href="{{ route('cases.edit', $case->id) }}" class="btn btn-warning">Edit</a>
                        @endcan
                        </div>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@stop

@section('footer')
@stop

@section('css')
@stop


@section('plugins.Sweetalert2', true)
@section('plugins.datatablesPlugins', true)
@section('plugins.Datatables', true)

@section('js')
    <script>
        function confirmDelete(button) {
            event.preventDefault();
            var row = $(button).closest("tr");
            var form = $(button).closest("form");
            Swal.fire({
                title: @json(__('Delete Case')),
                text: @json(__('Are you sure you want to delete this case?')),
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

        $(document).ready(function () {
            $('#bidsList').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                ordering: true,
                info: true,
                pageLength: 10,
                language: {
                    paginate: {
                        first: "First",
                        previous: "Previous",
                        next: "Next",
                        last: "Last"
                    }
                }
            });
        });
    </script>
@stop
