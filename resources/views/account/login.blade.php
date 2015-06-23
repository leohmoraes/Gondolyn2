@extends('layouts.standard')

@section('content')

    <div class="row">
        <div class="col-md-6 col-md-offset-3">
            <h2>Login</h2>
            <br>
            <form role="form" method="post" action="{{ URL::to('login/request') }}">
                <?php echo Form::token(); ?>
                <div class="form-group">
                    <label for="Email">Email</label>
                    <input type="email" name="email" class="form-control" id="Email" placeholder="Email Address">
                </div>
                <div class="form-group">
                    <label for="Password">Password</label>
                    <input type="password" name="password" class="form-control" id="Password" placeholder="Password">
                </div>
                <div class="checkbox">
                    <label for="RememberMe">
                        <input type="checkbox" name="remember_me" id="RememberMe"> Remember Me
                    </label>
                </div>
                <button type="submit" class="btn btn-primary raw-margin-top-24 raw-right">Login</button>
            </form>
            <button type="submit" class="btn btn-info raw-left raw-margin-top-24" onclick="window.location='{{ URL::to('forgot/password') }}'">Forgot Password</button>
        </div>
    </div>

@stop