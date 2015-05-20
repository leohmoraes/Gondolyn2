@extends('layouts.master')

@section('header')
<div class="raw100 raw-left">

</div>
@stop

@section('footer')
<div class="raw100 raw-left gondolyn-footer">
    <p class="raw-margin-left-20">&copy; {!! date('Y'); !!} <a href="http://mattlantz.ca">Matt Lantz</a> | v. <a href="{!! URL::to('change-log') !!}">{!! Gondolyn::version(); !!}</a></p>
</div>
@stop

@section('navigation')

<div class="raw100 raw-left raw-block-30 navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#mainNavbar">
            <span class="sr-only">{{ trans('content.app.name') }}</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>
        <a class="navbar-brand" href="{{ URL::to('/') }}">{{ trans('content.app.name') }}</a>
    </div>
    <div class="collapse navbar-collapse navbar-right" id="mainNavbar">
        <ul class="nav navbar-nav">
            <li class="dropdown">
                <a href="#" class="dropdown-toggle raw-margin-right-10" data-toggle="dropdown"><span class="fa fa-bars"></span> {{ trans("content.menu.account") }} <b class="caret"></b></a>
                <ul class="dropdown-menu">
                    @if (Session::get("logged_in"))
                        <li><a href="{{ URL::to(Session::get('role').'/home') }}"><span class="fa fa-home"></span> {{ trans("content.menu.home") }}</a></li>
                        <li><a href="{{ URL::to('account/settings') }}"><span class="fa fa-wrench"></span> {{ trans("content.menu.settings") }}</a></li>
                        @if (Session::get("role") == "admin")
                        <li><a href="{{ URL::to('admin/users') }}"><span class="fa fa-user"></span> {{ trans("content.menu.users") }}</a></li>
                        @endif
                        <li><a href="{{ URL::to('logout') }}"><span class="fa fa-sign-out"></span> {{ trans("content.menu.logout") }}</a></li>
                    @else
                        <li><a id="gondolynLoginPanel"><span class="fa fa-sign-in"></span> {{ trans("content.menu.login") }}</a></li>
                    @endif
                    {!! Module::getMenus(); !!}
                </ul>
            </li>
        </ul>
    </div><!--/.nav-collapse -->
</div>

@stop