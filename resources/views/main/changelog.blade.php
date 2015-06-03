@extends('layouts.standard')

@section('content')

    <div class="raw100 raw-left">
        <h2 class="text-center">Change Log</h2>
    </div>

    <table class="table table-striped">
    <thead>
    <tr>
        <th>Version</th>
        <th class="raw-m-hide raw-t-hide">Date</th>
        <th class="raw-m-hide">Commit</th>
        <th class="raw-m-hide raw-t-hide">Message</th>
    </tr>

    </thead>
    <tbody>
    @foreach ($changes as $change)
        <tr>
            <td>{{ $change->version }}</td>
            <td class="raw-m-hide raw-t-hide">{{ $change->date }}</td>
            <td class="raw-m-hide"><a href="{{ 'https://github.com/mlantz/Gondolyn2/commit/'.$change->commit }}">{{ $change->commit }}</a></td>
            <td class="raw-m-hide raw-t-hide">{{ $change->message }}</td>
        </tr>
    @endforeach
    </table>

@stop