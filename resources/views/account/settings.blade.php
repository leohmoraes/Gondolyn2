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

    <div class="raw100">
        @if (isset($adminEditorMode))
        <h2 class="text-center">User Settings</h2>
        @else
        <h2 class="text-center">My Settings</h2>
        @endif
    </div>

    <div class="raw100 rg-row raw-margin-top-24">
        <div class="rg-col-4 text-center raw-block-350">
            <div class="raw100 raw-left">
                <div class="gondolyn-profile-container">
                    <div class="gondolyn-profile" style="background-image: url({{ $profileImage }})" ></div>
                </div>
            </div>
        </div>

        <div class="rg-col-8">
            <div class="raw100 raw-left">
                @if (isset($adminEditorMode))
                    <button class="raw-right btn btn-danger raw-margin-left-24" data-toggle="modal" data-target="#adminDeleteModal">Delete Account</button>
                    @if ($user->user_active == "inactive")
                    <button class="raw-right btn btn-success raw-margin-left-24" data-toggle="modal" data-target="#adminActivateModal">Activate Account</button>
                    @else
                    <button class="raw-right btn btn-warning raw-margin-left-24" data-toggle="modal" data-target="#adminDeactivateModal">Deactivate Account</button>
                    @endif
                @else
                <button class="raw-right btn btn-danger raw-margin-left-24" data-toggle="modal" data-target="#deleteModal">Delete Account</button>
                    @if (Config::get("gondolyn.subscription"))
                    <a class="raw-right btn btn-default raw-margin-left-24" href="{{ URL::to('account/settings/subscription') }}">Subscription</a>
                    @endif
                @endif
            </div>
            @if (isset($adminEditorMode))
            <form id="userSettings" method="post" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ URL::to('admin/update') }}">
            @else
            <form id="userSettings" method="post" accept-charset="UTF-8" enctype="multipart/form-data" action="{{ URL::to('account/settings/update') }}">
            @endif
                <?= Form::token(); ?>
                <div class="raw100 raw-left raw-margin-top-24">
                    <div class="raw100 raw-left">
                        <label for="email">Email Address</label>
                        <input id="email" type="text" name="email" class="form-control" placeholder="Email Address" value="{{ $user->user_email }}">
                    </div>
                </div>
                <div class="raw100 raw-left raw-margin-top-24">
                    <div class="raw100 raw-left">
                        <label for="alt_email">Alt. Email Address</label>
                        <input id="alt_email" type="text" name="alt_email" class="form-control" placeholder="Alt. Email Address" value="{{ $user->user_alt_email }}">
                    </div>
                </div>
                <div class="raw100 raw-left raw-margin-top-24">
                    <div class="raw100 raw-left">
                        <label for="username">Username</label>
                        <input id="username" type="text" name="username" class="form-control" placeholder="Username" value="{{ $user->user_name }}">
                    </div>
                </div>

                {!! FormMaker::fromObject($shippingColumns, 'account.settings-row', $user); !!}

                @if (Session::get('role') == 'admin')
                <div class="raw100 raw-left raw-margin-top-24">
                    <div class="raw100 raw-left">
                        <label for="role">Role</label>
                        @include('account.select-role')
                    </div>
                </div>
                @endif

                <div class="raw50 raw-right text-right">
                    <input id="update" type="submit" class="btn btn-primary raw-right raw-margin-top-24 raw-margin-left-24" value="Save Settings">
                    @if ( ! isset($adminEditorMode))
                        <a href="{{ URL::to('account/settings/password') }}" class="btn raw-margin-top-24 btn-info">Change Password</a>
                    @endif
                </div>
            </form>

            <div class="raw50 raw-left raw-margin-top-24">

            </div>
        </div>
    </div>

@stop