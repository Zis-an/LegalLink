@extends('adminlte::page')

@section('title', 'Edit Bid')

@section('content_header')
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Edit Bid - {{ $bid->name }}</h1>
        </div>
        <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">Home</a></li>
                <li class="breadcrumb-item"><a href="{{ route('bids.index') }}">Bids</a></li>
                <li class="breadcrumb-item active">Edit Bid</li>
            </ol>
        </div>
    </div>
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form action="{{ route('bids.update', $bid->id) }}" method="POST">
                        @csrf
                        @method('PUT')

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul>
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <div class="form-group">
                            <label for="name">Name</label>
                            <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $bid->name) }}"
                                   placeholder="Enter bid name" required>
                        </div>

                        @can('bids.update')
                            <button type="submit" class="btn btn-primary">Update</button>
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

@section('js')
@stop
