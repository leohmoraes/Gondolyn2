@section('content')

<div class="raw25 raw-left raw-block-100"></div>

<div class="raw50 raw-left">
    <h2>Twitter Verification</h2>
    <p>Don't fret we only need this once to register your twitter account.</p>
    <br>
    <form role="form" method="post" action="{{ URL::to('login/twitter/verified') }}">
        <?php echo Form::token(); ?>
        <div class="form-group">
            <label for="Email">Email</label>
            <input type="email" name="email" class="form-control" id="Email" placeholder="Enter email">
        </div>
        <button type="submit" class="btn btn-primary raw-right">Login</button>
    </form>
</div>

@stop