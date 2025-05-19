@extends('adminlte::page')

@section('title', 'Create Consultation')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Create Consultation</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('consultations.index') }}">Consultations</a></li>
                <li class="breadcrumb-item active">Create Consultation</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                @can('consultations.create')
                    <div class="card-body">
                        <form action="{{ route('consultations.store') }}" method="POST">
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
                                <div class="form-group col-md-3">
                                    <label for="client_id">Client</label>
                                    <select name="client_id" class="form-control select2" required>
                                        <option value="">Select client</option>
                                        @foreach($clients as $client)
                                            <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>
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
                                            <option value="{{ $lawyer->id }}" {{ old('lawyer_id') == $lawyer->id ? 'selected' : '' }}>
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
                                            <option value="{{ $case->id }}" {{ old('case_id') == $case->id ? 'selected' : '' }}>
                                                {{ $case->title }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group col-md-3">
                                    <label for="status">Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="">Select status</option>
                                        <option value="Scheduled" {{ old('status') == 'Scheduled' ? 'selected' : '' }}>Scheduled</option>
                                        <option value="Completed" {{ old('status') == 'Completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="Missed" {{ old('status') == 'Missed' ? 'selected' : '' }}>Missed</option>
                                    </select>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="date_and_time">Date & Time</label>
                                    <input type="datetime-local" name="date_and_time" class="form-control"
                                           value="{{ old('date_and_time') }}" required>
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="mode">Mode</label>
                                    <select name="mode" class="form-control" required>
                                        <option value="">Select mode</option>
                                        <option value="physical" {{ old('mode') == 'physical' ? 'selected' : '' }}>Physical</option>
                                        <option value="virtual" {{ old('mode') == 'virtual' ? 'selected' : '' }}>Virtual</option>
                                    </select>
                                </div>

                            </div>

                            @can('consultations.create')
                                <button type="submit" class="btn btn-primary">Create</button>
                            @endcan

                            <a href="{{ route('consultations.index') }}" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                @endcan
            </div>
        </div>
    </div>
@stop

@section('footer')
@stop

@section('css')
@stop

@section('js')
@stop
