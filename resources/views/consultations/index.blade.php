@extends('adminlte::page')

@section('title', 'Consultations')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Consultations</h1>
            @can('consultations.create')
                <a href="{{ route('consultations.create') }}" class="btn btn-primary mt-2">Add new</a>
            @endcan
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Consultations</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('consultations.list')
                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="consultationsList" class="table  dataTable table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Client</th>
                                <th>Lawyer</th>
                                <th>Case</th>
                                <th>Date & Time</th>
                                <th>Mode</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($consultations as $consultation)
                                <tr>
                                    <td>{{ $consultation->client->user->name }}</td>
                                    <td>{{ $consultation->lawyer->user->name }}</td>
                                    <td>{{ $consultation->case->title }}</td>
                                    <td>{{ \Carbon\Carbon::parse($consultation->date_and_time)->format('d M Y, h:i A') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $consultation->mode === 'virtual' ? 'info' : 'secondary' }}">
                                            {{ ucfirst($consultation->mode) }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{
                                            $consultation->status === 'Completed' ? 'success' :
                                            ($consultation->status === 'Missed' ? 'danger' : 'warning')
                                        }}">
                                            {{ $consultation->status }}
                                        </span>
                                    </td>
                                    <td>
                                        <form action="{{ route('consultations.destroy', $consultation->id) }}" method="POST">
                                            @method('DELETE')
                                            @csrf
                                            @can('consultations.show')
                                                <a href="{{ route('consultations.show',['consultation'=>$consultation->id]) }}"
                                                   class="btn btn-info px-1 py-0 btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('consultations.update')
                                                <a href="{{route('consultations.edit',['consultation'=>$consultation->id])}}"
                                                   class="btn btn-warning px-1 py-0 btn-sm">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('consultations.delete')
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
                title: @json(__('Delete Consultation')),
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
            $('#consultationsList').DataTable({
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
