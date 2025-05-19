@extends('adminlte::page')

@section('title', 'Edit Permission')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Permission - {{ $permission->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
                <li class="breadcrumb-item active">Edit Permission</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('$permissions.update')
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('permissions.update', $permission->id) }}" method="POST">
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

                            <div class="form-group">
                                <label for="name">Name</label>
                                <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $permission->name) }}" placeholder="Enter permission name" required>
                            </div>

                            <div class="form-group">
                                <label for="guard">Guard</label>
                                <input type="text" name="guard_name" class="form-control" id="guard" value="{{ old('guard_name', $permission->guard_name) }}" placeholder="Enter guard name" required>
                            </div>

                            @can('permissions.update')
                                <button type="submit" class="btn btn-primary">Update</button>
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
