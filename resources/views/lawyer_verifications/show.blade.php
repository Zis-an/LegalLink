@extends('adminlte::page')

@section('title', 'View Verification')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>View Verification - {{ $verification->lawyer->user->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('lawyer-verifications.index') }}">Verifications</a></li>
                <li class="breadcrumb-item active">View Verification</li>
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

                        <div class="card">
                            <div class="card-body">
                                <p><strong>Lawyer:</strong> {{ $verification->lawyer->user->name }}</p>
                                <p><strong>Status:</strong> {{ $verification->status }}</p>
                                <p><strong>Reviewed By:</strong> {{ optional($verification->reviewer)->name ?? '-' }}</p>
                                <p><strong>Reviewed At:</strong> {{ $verification->reviewed_at ?? '-' }}</p>
                                <p><strong>Comment:</strong> {{ $verification->comment ?? '-' }}</p>

                                @if ($verification->document_path)
                                    <p><strong>Document:</strong>
                                        <a href="{{ asset('storage/' . $verification->document_path) }}" target="_blank">View Document</a>
                                    </p>
                                @endif
                            </div>
                        </div>

                        <form action="{{ route('lawyer-verifications.destroy', $verification->id) }}" method="POST" class="mt-4">
                        @csrf
                        @method('DELETE')

                        @can('lawyer_verifications.show')
                            <a href="{{ route('lawyer-verifications.index') }}" class="btn btn-info btn-sm">Go Back</a>
                        @endcan

                        @can('lawyer_verifications.update')
                            <a href="{{ route('lawyer-verifications.edit', $verification->id) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-pen"></i> Edit
                            </a>
                        @endcan

                        @can('lawyer_verifications.delete')
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
                title: @json(__('Delete Verification')),
                text: @json(__('Are you sure you want to delete this verification?')),
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
