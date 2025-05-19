@extends('adminlte::page')

@section('title', 'Edit Proposal')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Proposal</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bids.index') }}">Proposals</a></li>
                <li class="breadcrumb-item active">Edit Proposals</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('bids.update')
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('bids.update', $bid->id) }}" method="POST">
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
                                <input type="hidden" name="case_id" value="{{ $bid->case_id }}">

                                @if(auth()->user()->hasRole('lawyer'))
                                    <input type="hidden" name="lawyer_id" value="{{ $lawyer->id }}">
                                @elseif(auth()->user()->hasRole('admin'))
                                    <div class="form-group col-md-4">
                                        <label for="lawyer_id">Lawyer</label>
                                        <select name="lawyer_id" class="form-control select2" required>
                                            @if($lawyers->isNotEmpty())
                                                @foreach ($lawyers as $lawyerOption)
                                                    <option value="{{ $lawyerOption->id }}" {{ old('lawyer_id', $bid->lawyer_id) == $lawyerOption->id ? 'selected' : '' }}>
                                                        {{ $lawyerOption->user->name }}
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
                                    <input type="number" name="fee" class="form-control" required step="0.01"
                                           value="{{ old('fee', $bid->fee) }}" placeholder="Enter fees">
                                </div>

                                <div class="form-group col-md-4">
                                    <label for="time_estimated">Estimated Date</label>
                                    <input type="date" name="time_estimated" class="form-control" required
                                           value="{{ old('time_estimated', $bid->time_estimated) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label for="status">Case Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="pending" {{ old('status', $bid->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="accepted" {{ old('status', $bid->status) == 'accepted' ? 'selected' : '' }}>Accepted</option>
                                        <option value="rejected" {{ old('status', $bid->status) == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                    </select>
                                </div>
                            </div>


                            @can('bids.update')
                                <button type="submit" class="btn btn-primary">Update</button>
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
@stop

@section('js')
@stop
