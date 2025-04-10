@extends('adminlte::page')

@section('title', 'View Client')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Client - {{ $client->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('clients.index') }}">Clients</a></li>
                <li class="breadcrumb-item active">View Client</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
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
                        <label>Name</label>
                        <input type="text" class="form-control" value="{{ $client->name }}" disabled>
                    </div>

                    <form action="{{ route('clients.destroy', $client->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')

                        @can('clients.show')
                            <a href="{{ route('clients.index') }}" class="btn btn-info btn-sm">Go Back</a>
                        @endcan

                        @can('clients.update')
                            <a href="{{ route('clients.edit', $client->id) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-pen"></i> Edit
                            </a>
                        @endcan

                        @can('clients.delete')
                            <button type="button" onclick="confirmDelete(this)" class="btn btn-danger btn-sm">
                                <i class="fa fa-trash"></i> Delete
                            </button>
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


@section('plugins.Sweetalert2', true)

@section('js')
    <script>
        function confirmDelete(button) {
            event.preventDefault();
            var row = $(button).closest("tr");
            var form = $(button).closest("form");
            Swal.fire({
                title: @json(__('Delete Client')),
                text: @json(__('Are you sure you want to delete this client?')),
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
