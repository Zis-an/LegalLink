@extends('adminlte::page')

@section('title', 'Issues')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Issues</h1>
            @can('cases.create')
                <a href="{{ route('cases.create') }}" class="btn btn-primary mt-2">Add new</a>
            @endcan
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item active">Issues</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            @can('cases.list')
                <div class="card">
                    <div class="card-body table-responsive">
                        @if ($message = Session::get('success'))
                            <div class="alert alert-success">{{ $message }}</div>
                        @endif
                        <table id="casesList" class="table dataTable table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Client</th>
                                <th>Category</th>
                                <th>Status</th>
                                <th>Voice Note</th>
                                @if(auth()->user()->hasRole(['lawyer', 'admin'])) <th>Interest</th> @endif
                                @if(auth()->user()->hasRole(['client', 'admin'])) <th>Interested Lawyers</th> @endif
                                <th>Actions</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach ($cases as $case)
                                <tr>
                                    <td>{{ $case->client->user->name }}</td>
                                    <td>{{ ucfirst($case->category) }}</td>
                                    <td>
                                        @php
                                            $badge = match($case->status) {
                                                'open' => 'badge-primary',
                                                'in_progress' => 'badge-warning',
                                                'closed' => 'badge-success',
                                                default => 'badge-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $badge }}">{{ ucfirst(str_replace('_', ' ', $case->status)) }}</span>
                                    </td>
                                    <td>
                                        @if ($case->voice_note)
                                            <audio controls style="width: 100px;">
                                                <source src="{{ asset('storage/' . $case->voice_note) }}" type="audio/mpeg">
                                                Not supported
                                            </audio>
                                        @else
                                            <span class="text-muted">N/A</span>
                                        @endif
                                    </td>
                                    @if(auth()->user()->hasRole(['lawyer', 'admin']))
                                        <td>
                                            @php
                                                $hasBid = $case->bids->where('lawyer_id', optional(auth()->user()->lawyer)->id)->isNotEmpty();
                                            @endphp

                                            @if(!$hasBid)
                                                <a href="{{ route('bids.create', $case->id) }}" class="btn btn-success btn-sm">Interested</a>
                                            @else
                                                <button class="btn btn-success btn-sm pe-none" disabled>Interested</button>
                                            @endif
                                        </td>
                                    @endif
                                    @if(auth()->user()->hasRole(['client', 'admin']))
                                        <td>
                                            @if($case->bids->count() === 0)
                                                <span class="badge badge-pill badge-danger">None</span>
                                            @else
                                                <span class="badge badge-pill badge-success">{{ $case->bids->count() }} {{ $case->bids->count() == 1 ? "Lawyer" : "Lawyers" }}</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        @can('cases.show')
                                            <a href="{{ route('cases.show', $case->id) }}" class="btn btn-info btn-sm"><i class="fa fa-eye"></i></a>
                                        @endcan

                                        @can('cases.update')
                                            <a href="{{ route('cases.edit', $case->id) }}" class="btn btn-warning btn-sm"><i class="fa fa-pen"></i></a>
                                        @endcan

                                        <form action="{{ route('cases.destroy', $case->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')

                                        @can('cases.delete')
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
                title: @json(__('Delete Issue')),
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
            $('#casesList').DataTable({
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
