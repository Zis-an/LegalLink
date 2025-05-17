@extends('adminlte::page')

@section('title', config('app.name', 'Laravel'))

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    <p>Welcome to this beautiful admin panel.</p>
@stop

@section('css')
    {{-- Fonts and other custom CSS --}}
    <link rel="dns-prefetch" href="//fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito" rel="stylesheet">

    {{-- Optional: add a custom stylesheet --}}
    {{-- <link rel="stylesheet" href="{{ asset('css/custom.css') }}"> --}}
@stop

@section('plugins.datatablesPlugins', true)
@section('plugins.Datatables', true)
@section('plugins.Sweetalert2', true)

@section('js')
    {{-- CSRF Meta Tag (you can inject this via JS if needed) --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Vite scripts --}}
    @vite(['resources/js/app.js', 'resources/css/app.css'])

    <script> console.log("Hi, I'm using the Laravel-AdminLTE package with Vite!"); </script>
@stop
