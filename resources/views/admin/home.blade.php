@extends('layouts.standard')

@section('content')

<div class="raw100">
    <h2>Admin</h2>
    <br>
    <p>{{ $message }}</p>
</div>

@yield('form')

@stop