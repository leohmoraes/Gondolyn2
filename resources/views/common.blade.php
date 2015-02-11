<!-- Main content blocls -->

@section('header')
<div class="raw100 raw-left">

</div>
@stop

@section('footer')
<div class="raw100 raw-left gondolyn-footer">
    <p class="raw-margin-left-20">&copy; {!! date('Y'); !!} <a href="http://mattlantz.ca">Matt Lantz</a> | v. <a href="{{ URL::to('change-log') }}">{{ Gondolyn::version() }}</a></p>
</div>
@stop