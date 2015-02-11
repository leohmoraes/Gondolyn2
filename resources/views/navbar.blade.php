<!-- Nav Bar -->

@section('nav_bar')

<div class="raw100 raw-left raw-block-30 navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mainNavbar">
            <span class="sr-only">Gondolyn</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="{{ URL::to('/') }}">Gondolyn</a>
    </div>
    <div class="collapse navbar-collapse navbar-right" id="mainNavbar">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ trans("content.menu.account") }} <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    @if (Session::get("logged_in"))
                        <li><a href="{{ URL::to(Session::get('role').'/home') }}">{{ trans("content.menu.home") }}</a></li>
                        {!! Gondolyn::getModuleMenus(); !!}
                        <li><a href="{{ URL::to('user/settings') }}">{{ trans("content.menu.settings") }}</a></li>
                        @if (Session::get("role") == "admin")
                        <li><a href="{{ URL::to('admin/users') }}">{{ trans("content.menu.users") }}</a></li>
                        @endif
                        <li><a href="{{ URL::to('logout') }}">{{ trans("content.menu.logout") }}</a></li>
                    @else
                        <li><a id="gondolynLoginPanel">{{ trans("content.menu.login") }}</a></li>
                    @endif
                </ul>
            </li>
        </ul>
    </div><!--/.nav-collapse -->
</div>

@stop