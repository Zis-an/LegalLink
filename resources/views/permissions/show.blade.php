@extends('adminlte::page')

@section('title', 'View Permission')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Permission - {{ $permission->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('permissions.index') }}">Permissions</a></li>
                <li class="breadcrumb-item active">View Permission</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('permissions.show')
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
                            <label for="name">Name</label>
                            <input name="name" type="text" value="{{ $permission->name }}" disabled required
                                   class="form-control" id="name" placeholder="Enter name">
                        </div>
                        <div class="form-group">
                            <label for="guard">Guard</label>
                            <input type="text" name="guard_name" disabled required value="{{ $permission->guard_name }}"
                                   class="form-control" id="guard" placeholder="Enter guard name">
                        </div>

                        <form action="{{ route('permissions.destroy', $permission->id) }}" method="POST">
                            @method('DELETE')
                            @csrf
                            @can('permissions.list')
                                <a href="{{ route('permissions.index') }}" class="btn btn-info px-1 py-0 btn-sm">Go Back</a>
                            @endcan
                            <div class="d-flex justify-content-end">
                            @can('permissions.update')
                                <a href="{{ route('permissions.edit',['permission'=>$permission->id]) }}">
                                    <div  class="btn btn-warning px-3 border py-1">Edit</div>
                                </a>
                            @endcan
                            @can('permissions.delete')
                                <button onclick="isDelete(this)" class="btn btn-danger px-3 py-1 ml-2">Delete</button>
                            @endcan
                            </div>
                        </form>
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

@section('plugins.Sweetalert2', true)

@section('js')
    <script>
        function isDelete(button) {
            event.preventDefault();
            var row = $(button).closest("tr");
            var form = $(button).closest("form");
            Swal.fire({
                title: @json(__('Delete Permission')),
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
    </script>
@stop
