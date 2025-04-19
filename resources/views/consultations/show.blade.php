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
                        </div>

                        <form action="{{ route('consultations.destroy', $consultation->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')

                        @can('consultations.show')
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
