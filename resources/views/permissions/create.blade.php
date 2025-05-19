@extends('adminlte::page')

@section('title', 'Create Permission')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Create Permission</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
                <li class="breadcrumb-item active">Create Permission</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('permissions.create')
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('permissions.store') }}" method="POST">
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

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" id="name" placeholder="Enter permission name" required>
                            </div>

                            <input type="hidden" id="guard" name="guard_name" value="web" class="form-control">

                            @can('permissions.create')
                                <button type="submit" class="btn btn-primary">Create</button>
                            @endcan

                            <a href="{{ route('permissions.index') }}" class="btn btn-secondary">Cancel</a>
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
