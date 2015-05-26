@extends('layouts.navigation')

<div class="overlay"></div>

{!! Minify::stylesheet('/css/dashboard.css'); !!}

<div class="raw100 raw-left raw-margin-top-50">
@section('page-content')
    @yield('content')
@stop
</div>

@include('commons.footer')