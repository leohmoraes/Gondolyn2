@extends('layouts.standard')

@section('content')

<div class="raw100 text-center">
    <h2>Forgot Password</h2>
</div>

<div class="raw100 raw-margin-top-24">
    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <p>Please enter your email for us to send you a brand new password.</p>
            <form id="forgotPassword" method="post" action="{{ URL::to('forgot/password/request') }}">
                <?php echo Form::token(); ?>
                <div class="raw100 raw-left">
                    <label for="old_password">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="Your Email Address">
                </div>
                <div class="raw100 raw-left raw-margin-top-16">
                    <input id="update" type="submit" class="btn btn-primary raw-right" value="Send New Password">
                </div>
            </form>
        </div>
    </div>
</div>

@stop