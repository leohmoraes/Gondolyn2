@extends('layouts.standard')

@section('content')

    <div class="raw100 raw-left">
        <h2 class="text-center">Change Log</h2>
    </div>

    @foreach ($changes as $change)
        <div class="raw100 raw-left user-row">
            <div class="raw10 raw-left">
                <p>{{ $change->version }}</p>
            </div>
            <div class="raw10 raw-m-hide raw-t-hide raw-left">
                <p>{{ $change->date }}</p>
            </div>
            <div class="raw35 raw-m-hide raw-left">
                <a href="{{ 'https://github.com/mlantz/Gondolyn2/commit/'.$change->commit }}">{{ $change->commit }}</a>
            </div>
            <div class="raw45 raw-m-hide raw-t-hide raw-left">
                <p>{{ $change->message }}</p>
            </div>
        </div>
    @endforeach

@stop