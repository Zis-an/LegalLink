@extends('adminlte::page')

@section('title', 'Edit Verification')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Verification - {{ $verification->lawyer->user->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lawyer-verifications.index') }}">Verifications</a></li>
                <li class="breadcrumb-item active">Edit Verification</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('lawyer-verifications.update', $verification->id) }}" method="POST">
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
                            <div class="form-group col-md-12">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="Pending" {{ $verification->status == 'Pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="Approved" {{ $verification->status == 'Approved' ? 'selected' : '' }}>Approved</option>
                                    <option value="Rejected" {{ $verification->status == 'Rejected' ? 'selected' : '' }}>Rejected</option>
                                </select>
                            </div>

                            <div class="form-group col-md-12">
                                <label>Comment (for rejection)</label>
                                <textarea name="comment" class="form-control" rows="3">{{ old('comment', $verification->comment) }}</textarea>
                            </div>

                        @can('lawyer_verifications.update')
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
