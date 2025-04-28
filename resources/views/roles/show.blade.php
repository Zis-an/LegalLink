@extends('adminlte::page')

@section('title', 'View Role')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Role - {{ $role->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('roles.index') }}">Roles</a></li>
                <li class="breadcrumb-item active">View Role</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('roles.show')
                <div class="card card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="form-group">
                        <label>Name</label>
                        <input type="text" class="form-control" value="{{ $role->name }}" disabled>
                    </div>

                    <div class="form-group">
                        <label>Guard</label>
                        <input type="text" class="form-control" value="{{ $role->guard_name }}" disabled>
                    </div>

                    <h4 class="mt-4">Permissions</h4>

                    <div class="row">
                        @foreach ($permissions as $permission)
                            <div class="col-md-4">
                                <div class="custom-control custom-checkbox mb-2">
                                    <input type="checkbox" class="custom-control-input" id="permission_{{ $permission->id }}"
                                           value="{{ $permission->name }}" disabled
                                        {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="permission_{{ $permission->id }}">
                                        {{ $permission->name }}
                                    </label>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <form action="{{ route('roles.destroy', $role->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')

                        @can('roles.list')
                            <a href="{{ route('roles.index') }}" class="btn btn-info btn-sm">Go Back</a>
                        @endcan
                        @can('roles.update')
                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-pen"></i> Edit
                            </a>
                        @endcan
                        @can('roles.delete')
                            <button type="button" onclick="confirmDelete(this)" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Delete
                            </button>
                        @endcan
                    </form>
                </div>
            @endcan
        </div>
    </div>
@stop

@section('footer')
@stop

@section('css')
@stop


@section('plugins.Sweetalert2', true)

@section('js')
    <script>
        function confirmDelete(button) {
            event.preventDefault();
            var row = $(button).closest("tr");
            var form = $(button).closest("form");
            Swal.fire({
                title: @json(__('Delete Role')),
                text: @json(__('Are you sure you want to delete this role?')),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: @json(__('Delete')),
                cancelButtonText: @json(__('Cancel')),
            }).then((result) => {
                console.log(result)
                if (result.value) {
                    // Trigger the form submission
                    form.submit();
                }
            });
        }
    </script>
@stop
