@extends('layouts.standard')

@section('content')
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h1>Sample Module #1</h1>
            <p>This is an example of a module.</p>
            <br>
            <p>{{ $welcome }}</p>
            <br>
            <p>{{ Module::lang('sample.notifications.message.welcome') }}</p>
        </div>
    </div>
@stop
