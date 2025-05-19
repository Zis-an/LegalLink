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
@stop

@section('js')
@stop
