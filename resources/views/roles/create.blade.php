@extends('adminlte::page')

@section('title', 'Create Role')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Create Role</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Create Role</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('roles.create')
                <div class="card">
                    <div class="card-body">
                        <form action="{{ route('roles.store') }}" method="POST">
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
                                <input name="name" type="text" required class="form-control" id="name" placeholder="Enter role name">
                            </div>

                            <div class="form-group">
                                <label for="guard_name">Guard name</label>
                                <input name="guard_name" type="text" value="web" required class="form-control" id="guard_name" readonly>
                            </div>

                            <div class="form-group">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="select_all">
                                    <label class="custom-control-label" for="select_all">Select All Permissions</label>
                                </div>

                                <div class="row permissions">
                                    @foreach($permissions as $permission)
                                        <div class="col-md-4">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input permission-checkbox" id="permission_{{ $permission->id }}" name="permissions[]" value="{{ $permission->name }}">
                                                <label class="custom-control-label" for="permission_{{ $permission->id }}">{{ $permission->name }}</label>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            @can('roles.create')
                                <button class="btn btn-success" type="submit">Create</button>
                            @endcan

                            <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
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
    <script>
        document.getElementById('select_all').addEventListener('change', function () {
            const checkboxes = document.querySelectorAll('.permission-checkbox');
            checkboxes.forEach(checkbox => checkbox.checked = this.checked);
        });
    </script>
@stop
