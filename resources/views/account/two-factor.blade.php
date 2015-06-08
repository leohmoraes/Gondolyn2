@extends('layouts.standard')

@section('content')

<div class="raw25 raw-m-10 raw-left raw-block-100"></div>

<div class="raw50 raw-m-80 raw-left">
    <h2>Two Factor Login</h2>
    <br>
    <form role="form" method="post" action="{{ URL::to('account/two-factor/authenticate') }}">
        <?php echo Form::token(); ?>
        <div class="form-group">
            <label for="Email">Code</label>
            <input type="text" name="code" class="form-control" id="code" placeholder="Authentication Code">
        </div>
        <button type="submit" class="btn btn-primary raw-right">Authenticate</button>
    </form>
</div>

@stop

@section('javascript')

@if(Session::get('bad-code'))

<script type="text/javascript">

    gondolynNotify('That code was incorrect please try again.');

</script>
@endif

@stop