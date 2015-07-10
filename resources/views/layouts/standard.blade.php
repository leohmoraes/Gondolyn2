@extends('layouts.navigation')

@section('header')
<div class="raw100 raw-left">

</div>
@stop

@section('page-content')
<div class="main">
    <div class="raw100 raw-left raw-margin-top-50">
        <div class="raw-device raw-margin-auto">
            @yield("header")
        </div>
    </div>

    <div class="raw100 raw-left raw-margin-bottom-60">
        <div class="raw-device raw-margin-auto">
            @yield('content')
        </div>
    </div>
</div>

<div class="raw100 raw-left navbar navbar-inverse navbar-fixed-bottom">
    @include('commons.footer')
</div>
@stop
