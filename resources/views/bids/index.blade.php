@extends('adminlte::page')

@section('title', 'Bids')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Bids</h1>
            @can('bids.create')
                <a href="{{ route('bids.create') }}" class="btn btn-primary mt-2">Add new</a>
            @endcan
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Bids</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('bids.list')
                <div class="card">
                    <div class="card-body table-responsive">
                        <table id="bidsList" class="table  dataTable table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Bid</th>
                                <th width="80px">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($bids as $bid)
                                <tr>
                                    <td class="text-capitalize">{{ $bid->name }}</td>
                                    <td>
                                        <form action="{{ route('bids.destroy', $bid->id) }}" method="POST">
                                            @method('DELETE')
                                            @csrf
                                            @can('bids.show')
                                                <a href="{{ route('bids.show',['bid'=>$bid->id]) }}"
                                                   class="btn btn-info px-1 py-0 btn-sm">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                            @endcan
                                            @can('bids.update')
                                                <a href="{{route('bids.edit',['bid'=>$bid->id])}}"
                                                   class="btn btn-warning px-1 py-0 btn-sm">
                                                    <i class="fa fa-pen"></i>
                                                </a>
                                            @endcan
                                            @can('bids.delete')
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
                title: @json(__('Delete Bid')),
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
            $('#bidsList').DataTable({
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
