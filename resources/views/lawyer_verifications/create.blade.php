@extends('adminlte::page')

@section('title', 'Lawyer Verifications')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Lawyer Verifications</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lawyer-verifications.index') }}">Verifications</a></li>
                <li class="breadcrumb-item active">Create Verification</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('lawyer-verifications.store') }}" method="POST">
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
                                <label for="document">Upload Verification Document (PDF or Image)</label>
                                <input type="file" name="document" class="form-control" required accept=".pdf,.jpg,.jpeg,.png">
                            </div>
                        </div>
                        @can('lawyer_verifications.create')
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
