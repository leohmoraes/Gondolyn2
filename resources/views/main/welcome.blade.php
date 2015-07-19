@extends('layouts.standard')

@section('content')

<div class="raw100 raw-left">
    <h1>{{ trans('content.home.welcome') }}</h1>
    <br>
    <p>If you'd like to check out a great landing page for your app take a look at this theme which we've included as an example: <a href="{!! URL::to('pratt') !!}">Pratt</a></p>
</div>

@stop