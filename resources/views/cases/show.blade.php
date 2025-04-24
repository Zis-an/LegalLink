@extends('adminlte::page')

@section('title', 'View Case')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Case - {{ $case->title }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cases.index') }}">Cases</a></li>
                <li class="breadcrumb-item active">View Case</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        <div class="form-group col-md-4">
                            <label for="client_id">Client</label>
                            <select name="client_id" class="form-control select2" disabled>
                                <option>{{ $case->client->user->name }}</option>
                            </select>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="title">Case Title</label>
                            <input type="text" name="title" class="form-control" value="{{ old('title', $case->title) }}" disabled>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <strong>Category:</strong> {{ $case->category }}
                            </div>
                            <div class="col-md-6">
                                <strong>Subcategory:</strong> {{ $case->subcategory }}
                            </div>
                        </div>

                        <div class="form-group col-md-4">
                            <label for="status">Case Status</label>
                            <select name="status" class="form-control" disabled>
                                <option value="open" {{ $case->status == 'open' ? 'selected' : '' }}>Open</option>
                                <option value="in_progress" {{ $case->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                <option value="closed" {{ $case->status == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                    </div>



                    <div class="form-group">
                        <label for="description">Case Description</label>
                        <textarea name="description" rows="4" class="form-control" required>{{ old('description', $case->description) }}</textarea>
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

                    <div class="form-group">
                        <a href="{{ route('cases.index') }}" class="btn btn-primary">Go Back</a>
                        <a href="{{ route('cases.edit', $case->id) }}" class="btn btn-warning">Edit</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('footer')
@stop

@section('css')
@stop


@section('plugins.Sweetalert2', true)

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
    </script>
@stop
