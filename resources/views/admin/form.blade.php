@section('form')

<div class="raw100">
    <h2>Admin : FormMaker Example</h2>
</div>

<div class="raw100">

    {!! Form::open(array('files'=> true, 'url' => 'admin/submit/form')) !!}

    <div class="form-group">
        <label>Automobile</label>
        <input class="form-control typeahead" name="automobile" placeholder="Brand of Automobile" value="{!! Validation::value('automobile') !!}">
    </div>

    {!! FormMaker::fromTable('users', null, Config::get('forms.billing')); !!}

    {!! Form::submit('Submit') !!}

    {!! Form::close() !!}

</div>

@stop

@section('javascript')

    {!! Minify::javascript(url('/js/typeahead.bundle.js')) !!}

    @parent

    <script type="text/javascript">

    var cars = [
        'Audi',
        'Mercedes',
        'Bently',
        'Volvo',
        'BMW',
        'Ford',
        'Honda',
        'Toyota',
        'GMC'
    ];

    $('.typeahead').typeahead({
        minLength: 1,
        highlight: true
    },
    {
        name: 'cars',
        source: typeaheadMatcher(cars)
    });
    </script>

@stop