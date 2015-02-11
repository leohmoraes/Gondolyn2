@section('gondolyn_login')

<div class="gondolyn-login">

    <div class="raw100 raw-left raw-margin-top-48 raw-margin-bottom-48">
        <div class="raw33 raw-left text-centered">
            <a href="{{ URL::to('login/email') }}">
                <div class="gondolyn-login-icon gondolyn-login-email"></div>
                Email
            </a>
        </div>
        @if (Config::get("gondolyn.smLogin"))
        <div class="raw33 raw-left text-centered">
            <a href="{{ URL::to('login/facebook') }}">
                <div class="gondolyn-login-icon gondolyn-login-facebook"></div>
                Facebook
            </a>
        </div>
        <div class="raw33 raw-left text-centered">
            <a href="{{ URL::to('login/twitter') }}">
                <div class="gondolyn-login-icon gondolyn-login-twitter"></div>
                Twitter
            </a>
        </div>
        @endif
    </div>

</div>

@stop