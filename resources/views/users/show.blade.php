@extends('adminlte::page')

@section('title', 'Show User')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Show User</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('users.index') }}">Users</a></li>
                <li class="breadcrumb-item active">Show User</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="form-group">
                        <strong>Name:</strong>
                        <p>{{ $user->name }}</p>
                    </div>

                    <div class="form-group">
                        <strong>Email:</strong>
                        <p>{{ $user->email }}</p>
                    </div>

                    <div class="form-group">
                        <strong>Roles:</strong>
                        @if(!empty($user->getRoleNames()))
                            <div class="row">
                                @foreach($user->getRoleNames() as $role)
                                    <div class="col-md-4">
                                        <span class="badge badge-success">{{ $role }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <p>No roles assigned</p>
                        @endif
                    </div>

                    <div class="form-group">
                        <a href="{{ route('users.index') }}" class="btn btn-primary">Back</a>
                    </div>
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
