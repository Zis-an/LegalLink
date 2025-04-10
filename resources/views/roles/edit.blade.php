@extends('adminlte::page')

@section('title', 'Update Role')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Update Roles - {{ $role->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">Update Roles</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{route('roles.update',['role'=>$role->id])}}" method="POST">
                        @method('PUT')
                        @csrf

                        @if (count($errors) > 0)
                            <div class = "alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input name="name" value="{{$role->name}}" type="text" required class="form-control" id="name" placeholder="Enter role name">
                        </div>

                        <div class="form-group">
                            <label for="guard_name">Guard Name</label>
                            <input name="guard_name" type="text" value="{{ $role->guard_name }}" required class="form-control" id="guard_name" placeholder="Enter guard name" readonly>
                        </div>

                        <h4>Permissions</h4>
                        <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="select_all"
                                {{ checkRolePermissions($role, $permissions) ? 'checked' : '' }}>
                            <label for="select_all" class="custom-control-label">Select All</label>
                        </div>

                        <hr>

                        <div class="form-group row permissions">
                            @foreach($permissions as $permission)
                                <div class="custom-control custom-checkbox col-md-4">
                                    <input class="custom-control-input" type="checkbox" id="permission_{{ $permission->id }}"
                                           {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}
                                           name="permissions[]" value="{{ $permission->name }}">
                                    <label for="permission_{{ $permission->id }}" class="custom-control-label">{{ $permission->name }}</label>
                                </div>
                            @endforeach
                        </div>

                        @can('roles.update')
                            <button class="btn btn-success" type="submit">Update</button>
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
    <script>
        $('#select_all').on('click', function () {
            let checked = $(this).is(':checked');
            $('.permissions input[type="checkbox"]').prop('checked', checked);
        });

        $('.permissions input[type="checkbox"]').on('click', function () {
            let allChecked = $('.permissions input[type="checkbox"]').length === $('.permissions input[type="checkbox"]:checked').length;
            $('#select_all').prop('checked', allChecked);
        });
    </script>
@stop
