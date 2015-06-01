@extends('layouts.navigation')

@section('page-content')

    <div class="overlay"></div>

    {!! Minify::stylesheet(url('/css/dashboard.css')); !!}

    <div class="raw100 raw-left raw-margin-top-50">
        @yield('content')
    </div>

    @include('commons.footer')
@stop

@section('javascript')
    {!! Minify::javascript(url('/js/dashboard.js')); !!}
@stop
