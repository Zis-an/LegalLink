@extends('adminlte::page')

@section('title', 'Create Bid')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Create Bid</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bids.index') }}">Bids</a></li>
                <li class="breadcrumb-item active">Create Bid</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
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
                            <div class="form-group col-md-4">
                                <label for="case_id">Case</label>
                                <select name="case_id" class="form-control select2" required>
                                    @if($cases->isNotEmpty())
                                    @foreach ($cases as $case)
                                        <option value="{{ $case->id }}" {{ old('case_id') == $case->id ? 'selected' : '' }}>
                                            {{ $case->title }}
                                        </option>
                                    @endforeach
                                    @else
                                        <option value="">No cases available</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="lawyer_id">Lawyer</label>
                                <select name="lawyer_id" class="form-control select2" required>
                                    @if($lawyers->isNotEmpty())
                                    @foreach ($lawyers as $lawyer)
                                        <option value="{{ $lawyer->id }}" {{ old('lawyer_id') == $lawyer->id ? 'selected' : '' }}>
                                            {{ $lawyer->user->name }}
                                        </option>
                                    @endforeach
                                    @else
                                        <option value="">No lawyers available</option>
                                    @endif
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="fee">Fees</label>
                                <input type="number" name="fee" class="form-control" required value="{{ old('fee') }}" placeholder="Enter fees">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="time_estimated">Estimated Date</label>
                                <input type="date" name="time_estimated" class="form-control" required value="{{ old('time_estimated') }}">
                            </div>

                            <div class="form-group col-md-6">
                                <label for="status">Case Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="accepted" {{ old('status') == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                    <option value="rejected" {{ old('status') == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>
                        </div>

                        @can('bids.create')
                            <button type="submit" class="btn btn-primary">Create</button>
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
