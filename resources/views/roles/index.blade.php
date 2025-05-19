@extends('adminlte::page')

@section('title', 'Roles')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Roles</h1>
            @can('roles.create')
                <a href="{{ route('roles.create') }}" class="btn btn-primary mt-2">Add New</a>
            @endcan
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Roles</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('roles.list')
                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="rolesList" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Guard</th>
                                <th>Permissions</th>
                                <th class="text-center" width="90">Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ $role->name }}</td>
                                    <td>{{ $role->guard_name }}</td>
                                    <td>
                                        @foreach($role->permissions as $permission)
                                            <span class="badge badge-success text-capitalize">{{ $permission->name }}</span>
                                        @endforeach
                                    </td>
                                    <td class="text-center">
                                        <form action="{{ route('roles.destroy', $role->id) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        @can('roles.show')
                                            <a href="{{ route('roles.show', $role->id) }}" class="btn btn-info btn-sm px-1 py-0">
                                                <i class="fa fa-eye"></i>
                                            </a>
                                        @endcan
                                        @can('roles.update')
                                            <a href="{{ route('roles.edit', $role->id) }}" class="btn btn-warning btn-sm px-1 py-0">
                                                <i class="fa fa-pen"></i>
                                            </a>
                                        @endcan
                                        @can('roles.delete')
                                            <button onclick="isDelete(this)" class="delete-button btn btn-danger btn-sm px-1 py-0">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        @endcan
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
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

@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('js')
    <script>
        function isDelete(button) {
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

        $(document).ready(function () {
            $('#rolesList').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                ordering: true,
                info: true,
                pageLength: 10,
                language: {
                    paginate: {
                        first: "First",
                        previous: "Previous",
                        next: "Next",
                        last: "Last"
                    }
                }
            });
        });
    </script>
@stop
