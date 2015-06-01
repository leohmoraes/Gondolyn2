@extends('layouts.navigation')

<div class="overlay"></div>

{!! Minify::stylesheet(url('/css/dashboard.css')); !!}

<div class="raw100 raw-left raw-margin-top-50">
@section('page-content')
    @yield('content')
@stop
</div>

@section('javascript')
    {!! Minify::javascript(url('/js/dashboard.js')); !!}
@stop

@include('commons.footer')