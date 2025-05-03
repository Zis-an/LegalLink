@extends('adminlte::page')

@section('title', 'User List')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>User List</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Users</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('users.list')
                <div class="card">
                    <div class="card-body">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">{{ $message }}</div>
                        @endif

                        @can('users.create')
                            <a href="{{ route('users.create') }}" class="btn btn-primary mb-3">Create User</a>
                        @endcan

                        <table id="usersList" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Roles</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($data as $user)
                                <tr>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>
                                        @foreach($user->roles as $role)
                                            @php
                                                $badgeClass = match ($role->name) {
                                                    'admin' => 'badge-primary',
                                                    'Lawyer' => 'badge-warning',
                                                    default => 'badge-secondary',
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">{{ $role->name }}</span>
                                        @endforeach
                                    </td>
                                    <td>
                                        @can('users.update')
                                            <a href="{{ route('users.edit', $user->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i></a>
                                        @endcan

                                        <form action="{{ route('users.destroy', $user->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            @can('users.delete')
                                                <button type="submit" class="btn btn-danger btn-sm" onclick="isDelete(this)">
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
            $('#usersList').DataTable({
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
