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
                            <div class="form-group col-md-4">
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

                            <div class="form-group col-md-4">
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

                            <div class="form-group col-md-4">
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

@section('js')
@stop
