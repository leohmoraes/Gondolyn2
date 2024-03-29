@extends('layouts.standard')

@section('content')

    @if (isset($adminEditorMode))
        @include('admin.modals')
    @endif

    <!-- Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Delete My Account</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you wish to delete your account? Please remember you cannot undo this action.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <a id="deleteBtn" type="button" class="btn btn-danger" href="{{ URL::to('account/delete/account') }}">Confirm Delete</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Content -->

    <div class="raw100 rg-row raw-margin-top-24">
        <div class="rg-col-4 text-center">
            <div class="raw100 raw-left raw-margin-top-24 raw-margin-bottom-48">
                <div class="gondolyn-profile-container">
                    <div class="gondolyn-profile" style="background-image: url({{ $profileImage }})" ></div>
                </div>
            </div>
        </div>

        <div class="rg-col-8">
            <div class="tab-panel">

                @include('account.tab-menu', [
                    'settingsTab' => true
                ])

            </div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="settings">
                    <div class="raw100 raw-left raw-margin-top-24">
                        @if (! isset($newAccount))
                            @if (isset($adminEditorMode))
                                <button class="raw-right btn btn-danger raw-margin-left-24" data-toggle="modal" data-target="#adminDeleteModal">Delete Account</button>
                                @if ($user->user_active === "inactive")
                                <button class="raw-right btn btn-success raw-margin-left-24" data-toggle="modal" data-target="#adminActivateModal">Activate Account</button>
                                @else
                                <button class="raw-right btn btn-warning raw-margin-left-24" data-toggle="modal" data-target="#adminDeactivateModal">Deactivate Account</button>
                                @endif
                            @else
                            <button class="raw-right btn btn-danger raw-margin-left-24" data-toggle="modal" data-target="#deleteModal">Delete Account</button>
                            @endif
                        @endif
                    </div>
                    @if ( ! isset($newAccount))
                    <div class="raw100 raw-left raw-margin-top-24">
                        <div class="raw100 raw-left">
                            <label for="appCode">API Authorization Code</label>
                            <input id="appCode" disabled type="text" class="form-control" value="{{ AccountServices::appAuthCode($user->id) }}">
                        </div>
                    </div>
                    @endif
                    @if (isset($adminEditorMode) && ! isset($newAccount))
                    <form id="userSettings" method="post" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ URL::to('admin/users/update') }}" autocomplete="false" autocomplete="off">
                    @elseif (isset($adminEditorMode) && isset($newAccount))
                    <form id="userSettings" method="post" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ URL::to('admin/users/generate') }}" autocomplete="false" autocomplete="off">
                    @else
                    <form id="userSettings" method="post" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ URL::to('account/settings/update') }}" autocomplete="false" autocomplete="off">
                    @endif
                        <?= Form::token(); ?>

                        {!! FormMaker::fromObject(Config::get('forms.account'), 'account.settings-row', $user); !!}

                        @if (Session::get('role') == 'admin')
                        <div class="raw100 raw-left raw-margin-top-24">
                            <div class="raw100 raw-left">
                                <label for="role">Role</label>
                                @include('account.select-role')
                            </div>
                        </div>
                        @endif

                        @if ( ! isset($newAccount))
                        <div class="raw100 raw-left raw-margin-top-24">
                            <h3>Billing Information</h3>
                        </div>
                        {!! FormMaker::fromObject($billingColumns, 'account.settings-row', $user); !!}
                        @endif

                        @if(Config::get('gondolyn.twoFactorAuthentication.enabled'))
                        <div class="raw100 raw-left raw-margin-top-24">
                            <h3>Two Factor Authentication</h3>
                        </div>
                        {!! FormMaker::fromObject(Config::get('forms.two-factor'), 'account.settings-row', $user); !!}
                        @endif

                        <div class="raw50 raw-right text-right">
                            <input id="update" type="submit" class="btn btn-primary raw-right raw-margin-top-24 raw-margin-left-24" value="Save Settings">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop

@section('javascript')

    @parent

    {!! Minify::javascript('/js/typeahead.bundle.js')->withFullUrl() !!}

    {!! Minify::javascript('/js/accounts/settings.js')->withFullUrl() !!}

@stop