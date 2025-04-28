@extends('adminlte::page')

@section('title', 'View Lawyer')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Lawyer - {{ $lawyer->user->name }}</h1>
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
            @can('lawyers.show')
                <div class="card">
                    <div class="card-body">
                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Name</label>
                                <input type="text" class="form-control" value="{{ $lawyer->user->name }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Email</label>
                                <input type="text" class="form-control" value="{{ $lawyer->user->email }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Bar ID</label>
                                <input type="text" class="form-control" value="{{ $lawyer->bar_id }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Practice Area</label>
                                <input type="text" class="form-control" value="{{ $lawyer->practice_area }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Chamber Name</label>
                                <input type="text" class="form-control" value="{{ $lawyer->chamber_name }}" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Chamber Address</label>
                                <textarea class="form-control" rows="2" disabled>{{ $lawyer->chamber_address }}</textarea>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Photo</label><br>
                                @if ($lawyer->photo)
                                    <img src="{{ asset('storage/' . $lawyer->photo) }}" class="img-thumbnail" width="120">
                                @else
                                    <p class="text-muted">No photo</p>
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('lawyers.destroy', $lawyer->id) }}" method="POST" class="mt-4">
                            @csrf
                            @method('DELETE')

                            @can('lawyers.list')
                                <a href="{{ route('lawyers.index') }}" class="btn btn-info btn-sm">Go Back</a>
                            @endcan

                            @can('lawyers.update')
                                <a href="{{ route('users.edit', $lawyer->user_id) }}" class="btn btn-warning btn-sm">
                                    <i class="fa fa-pen"></i> Edit
                                </a>
                            @endcan

                            @can('lawyers.delete')
                                <button type="button" onclick="isDelete(this)" class="btn btn-danger btn-sm">
                                    <i class="fa fa-trash"></i> Delete
                                </button>
                            @endcan
                        </form>
                    </div>
                </div>
            @endcan
        </div>
    </div>
@stop

@section('plugins.Sweetalert2', true)

@section('js')
    <script>
        function isDelete(button) {
            event.preventDefault();
            var form = $(button).closest("form");
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
    </script>
@stop
