@extends('layouts.standard')

@section('stylesheets')

    {!! Minify::stylesheet(Module::asset('sample', 'css/sample.css', 'text/css')) !!}

@stop

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

    <div class="row raw-margin-top-24">
        <div class="col-md-6 col-md-offset-3">
            <table class="table table-striped">
                <tr>
                    <th><a href="{!! URL::to('sample?sort=id|'.$dir) !!}"><span class="fa fa-sort"></span> ID</a></th>
                    <th><a href="{!! URL::to('sample?sort=created_at|'.$dir) !!}"><span class="fa fa-sort"></span> Created At</a></th>
                    <th><a href="{!! URL::to('sample?sort=updated_at|'.$dir) !!}"><span class="fa fa-sort"></span> Edited At</a></th>
                </tr>
                @foreach($samples as $sample)
                    <tr data-id="{{ $sample->id }}">
                        <td><input disabled class="table-input" type="text" data-column="id" value="{{ $sample->id }}" /></td>
                        <td><input class="table-input" type="text" data-column="created_at" value="{{ $sample->created_at }}" /></td>
                        <td><input class="table-input" type="text" data-column="updated_at" value="{{ $sample->updated_at }}" /></td>
                    </tr>
                @endforeach

            </table>
        </div>
    </div>
@stop

@section('javascript')

    {!! Minify::javascript(Module::asset('sample', 'js/sample.js')) !!}

@stop
