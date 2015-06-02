<ul class="nav nav-tabs" role="tablist">
    <li role="presentation" {!! (isset($settingsTab)) ? 'class="active"': '' !!}><a href="{!! URL::to('account/settings') !!}" role="tab" >Settings</a></li>
    @if ( ! isset($adminEditorMode))
    <li role="presentation" {!! (isset($passwordTab)) ? 'class="active"': '' !!}><a href="{!! URL::to('account/settings/password') !!}" role="tab">Password</a></li>
    @endif
    @if (Config::get("gondolyn.subscription") && ! isset($adminEditorMode))
    <li role="presentation" {!! (isset($subscriptionTab)) ? 'class="active"': '' !!}><a href="{!! URL::to('account/settings/subscription') !!}" role="tab">Subscription</a></li>
    @endif
</ul>