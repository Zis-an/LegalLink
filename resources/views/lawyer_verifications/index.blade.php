@extends('adminlte::page')

@section('title', 'Lawyer Verifications')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Lawyer Verifications</h1>
            @can('lawyer_verifications.create')
                <a href="{{ route('lawyer-verifications.create') }}" class="btn btn-primary mt-2">Add new</a>
            @endcan
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Verifications</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('lawyer_verifications.list')
                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="verificationsList" class="table dataTable table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Lawyer</th>
                                <th>Status</th>
                                <th>Reviewed By</th>
                                <th>Reviewed At</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($verifications as $verification)
                                <tr>
                                    <td>{{ $verification->id }}</td>
                                    <td>{{ $verification->lawyer->user->name }}</td>
                                    <td>{{ $verification->status }}</td>
                                    <td>{{ optional($verification->reviewer)->name ?? '-' }}</td>
                                    <td>{{ $verification->reviewed_at ?? '-' }}</td>
                                    <td>
                                        <form action="{{ route('lawyer-verifications.destroy', $verification->id) }}" method="POST">
                                            @method('DELETE')
                                            @csrf
                                            @can('lawyer-verifications.show')
                                                <a href="{{ route('lawyer-verifications.show',['consultation'=>$verification->id]) }}"
                                                   class="btn btn-info px-1 py-0 btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('lawyer-verifications.update')
                                                <a href="{{route('lawyer-verifications.edit',['consultation'=>$verification->id])}}"
                                                   class="btn btn-warning px-1 py-0 btn-sm">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('lawyer-verifications.delete')
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

@section('footer')
@stop

@section('css')
@stop

@section('plugins.datatablesPlugins', true)
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('js')
    <script>
        function isDelete(button) {
            event.preventDefault();
            var row = $(button).closest("tr");
            var form = $(button).closest("form");
            Swal.fire({
                title: @json(__('Delete Verification')),
                text: @json(__('Are you sure you want to delete this?')),
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
            $('#verificationsList').DataTable({
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
