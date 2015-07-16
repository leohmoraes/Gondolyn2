@extends('layouts.navigation')

@section('page-content')

    <div class="overlay"></div>

    {!! Minify::stylesheet('/css/dashboard.css')->withFullUrl() !!}

    <div class="raw100 raw-left raw-margin-top-50">
        @yield('content')
    </div>

    @include('commons.footer')
@stop

@section('javascript')
    {!! Minify::javascript('/js/dashboard.js')->withFullUrl() !!}
@stop
