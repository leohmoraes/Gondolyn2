@section('gondolyn_login')

<div class="gondolyn-login">

    <div class="raw100 raw-left raw-margin-top-48 raw-margin-bottom-48">
        <div class="raw33 raw-left text-center">
            <a href="{{ URL::to('login/email') }}">
                <div class="gondolyn-login-icon-wrapper">
                    <span class="fa fa-envelope-o gondolyn-login-icon"></span>
                </div>
                <span class="gondolyn-login-text">Email</span>
            </a>
        </div>
        @if (Config::get("gondolyn.smLogin"))
        <div class="raw33 raw-left text-center">
            <a href="{{ URL::to('login/facebook') }}">
                <div class="gondolyn-login-icon-wrapper">
                    <span class="fa fa-facebook gondolyn-login-icon"></span>
                </div>
                <span class="gondolyn-login-text">Facebook</span>
            </a>
        </div>
        <div class="raw33 raw-left text-center">
            <a href="{{ URL::to('login/twitter') }}">
                <div class="gondolyn-login-icon-wrapper">
                    <span class="fa fa-twitter gondolyn-login-icon"></span>
                </div>
                <span class="gondolyn-login-text">Twitter</span>
            </a>
        </div>
        @endif
    </div>

</div>

@stop