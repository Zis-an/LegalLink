@extends('adminlte::page')

@section('title', 'Edit Case')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Case</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cases.index') }}">Cases</a></li>
                <li class="breadcrumb-item active">Edit Case</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('cases.update', $case->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="form-group col-md-4">
                                <label for="client_id">Client</label>
                                <select name="client_id" class="form-control select2" required>
                                    @foreach ($clients as $client)
                                        <option value="{{ $client->id }}" {{ $case->client_id == $client->id ? 'selected' : '' }}>
                                            {{ $client->user->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="title">Case Title</label>
                                <input type="text" name="title" class="form-control" value="{{ old('title', $case->title) }}" required>
                            </div>

                            <div class="form-group col-md-4">
                                <label for="status">Case Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="open" {{ $case->status == 'open' ? 'selected' : '' }}>Open</option>
                                    <option value="in_progress" {{ $case->status == 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                    <option value="closed" {{ $case->status == 'closed' ? 'selected' : '' }}>Closed</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">Case Description</label>
                            <textarea name="description" rows="4" class="form-control" required>{{ old('description', $case->description) }}</textarea>
                        </div>

                        <div class="form-group">
                            <label for="voice_note">Voice Note</label><br>
                            @if ($case->voice_note)
                                <audio controls>
                                    <source src="{{ asset('storage/' . $case->voice_note) }}" type="audio/mpeg">
                                    Your browser does not support the audio element.
                                </audio>
                                <br>
                            @endif
                            <input type="file" name="voice_note" class="form-control mt-2" accept="audio/*">
                            <small class="text-muted">Leave blank if you don't want to change the voice note</small>
                        </div>

                        <button type="submit" class="btn btn-success">Update Case</button>
                        <a href="{{ route('cases.index') }}" class="btn btn-secondary">Cancel</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container .select2-selection--single {
            box-sizing: border-box;
            cursor: pointer;
            display: block;
            height: 37px; /* Set to your desired height */
            user-select: none;
            -webkit-user-select: none;
        }

        /* Adjust the line-height of the rendered text inside Select2 */
        .select2-container--classic .select2-selection--single .select2-selection__rendered {
            line-height: 34px; /* Adjust line height */
        }

        /* Style the Select2 arrow and set its height */
        .select2-container--classic .select2-selection--single .select2-selection__arrow {
            background-color: #ddd;
            border: none;
            border-left: 1px solid #aaa;
            border-top-right-radius: 4px;
            border-bottom-right-radius: 4px;
            height: 35px; /* Set height for the arrow */
            position: absolute;
            top: 1px;
            right: 1px;
            width: 20px;
            background-image: -webkit-linear-gradient(top, #eeeeee 50%, #cccccc 100%);
            background-image: -o-linear-gradient(top, #eeeeee 50%, #cccccc 100%);
            background-image: linear-gradient(to bottom, #eeeeee 50%, #cccccc 100%);
            background-repeat: repeat-x;
            filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#FFEEEEEE', endColorstr='#FFCCCCCC', GradientType=0);
        }
    </style>
@stop

@section('js')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
@stop
