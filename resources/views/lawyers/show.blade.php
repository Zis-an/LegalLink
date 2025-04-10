@extends('adminlte::page')

@section('title', 'View Lawyer')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Lawyer - {{ $lawyer->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lawyers.index') }}">Lawyers</a></li>
                <li class="breadcrumb-item active">View Lawyer</li>
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
                        <input type="text" class="form-control" value="{{ $lawyer->name }}" disabled>
                    </div>

                    <form action="{{ route('lawyers.destroy', $lawyer->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')

                        @can('lawyers.show')
                            <a href="{{ route('lawyers.index') }}" class="btn btn-info btn-sm">Go Back</a>
                        @endcan

                        @can('lawyers.update')
                            <a href="{{ route('lawyers.edit', $lawyer->id) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-pen"></i> Edit
                            </a>
                        @endcan

                        @can('lawyers.delete')
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
                title: @json(__('Delete Lawyer')),
                text: @json(__('Are you sure you want to delete this lawyer?')),
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
