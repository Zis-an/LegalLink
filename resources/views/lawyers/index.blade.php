@extends('adminlte::page')

@section('title', 'Lawyers')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Lawyers</h1>
            @can('lawyers.create')
                <a href="{{ route('users.create') }}" class="btn btn-primary mt-2">Add new</a>
            @endcan
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Lawyers</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @if ($message = Session::get('success'))
                <div class="alert alert-success">{{ $message }}</div>
            @endif

            @can('lawyers.list')
                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="lawyersList" class="table dataTable table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>SL</th>
                                <th>Photo</th>
                                <th>User Name</th>
                                <th>Bar ID</th>
                                <th>Practice Area</th>
                                <th>Chamber Name</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($lawyers as $lawyer)
                                <tr>
                                    <td>{{ ++$i }}</td>
                                    <td><img src="{{ asset('storage/' . $lawyer->photo) }}" width="50"></td>
                                    <td class="text-capitalize">{{ $lawyer->user->name }}</td>
                                    <td>{{ $lawyer->bar_id }}</td>
                                    <td>{{ ucfirst($lawyer->practice_area) }}</td>
                                    <td>{{ $lawyer->chamber_name }}</td>
                                    <td>
                                        <form action="{{ route('lawyers.destroy', $lawyer->id) }}" method="POST">
                                            @csrf
                                            @method('DELETE')

                                            @can('lawyers.show')
                                                <a href="{{ route('lawyers.show', $lawyer->id) }}"
                                                   class="btn btn-info px-1 py-0 btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('lawyers.update')
                                                <a href="{{ route('users.edit', $lawyer->user_id) }}"
                                                   class="btn btn-warning px-1 py-0 btn-sm">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('lawyers.delete')
                                                <button onclick="isDelete(this)"
                                                        class="btn btn-danger btn-sm px-1 py-0">
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

@section('plugins.datatablesPlugins', true)
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('js')
    <script>
        function isDelete(button) {
            event.preventDefault();
            const form = $(button).closest("form");
            Swal.fire({
                title: @json(__('Delete Lawyer')),
                text: @json(__('Are you sure you want to delete this?')),
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: @json(__('Delete')),
                cancelButtonText: @json(__('Cancel')),
            }).then((result) => {
                if (result.value) {
                    form.submit();
                }
            });
        }

        $(document).ready(function () {
            $('#lawyersList').DataTable({
                responsive: true,
                lengthChange: false,
                autoWidth: false,
                ordering: true,
                info: true,
                pageLength: 10
            });
        });
    </script>
@stop
