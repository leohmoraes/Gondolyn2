@section('form')

<div class="raw100">
    <h2>Admin : Form Example</h2>
</div>

<div class="raw100">

    {!! Form::open(array('url' => 'admin/submit/form')) !!}

    {!! FormMaker::fromTable('users', null, Config::get('forms.shipping')); !!}

    {!! Form::submit('Submit') !!}

    {!! Form::close() !!}

</div>

@stop