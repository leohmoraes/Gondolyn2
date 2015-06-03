@extends('layouts.standard')

@section('content')

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
                    'passwordTab' => true
                ])

            </div>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="password">

                    <form id="userSettings" method="post" action="{{ URL::to('account/settings/update/password') }}">
                        <?= Form::token(); ?>
                        <div class="raw100 raw-left rg-row raw-margin-top-24">
                            <label for="old_password">Old Password</label>
                            <input id="old_password" type="password" name="old_password" class="form-control" placeholder="Old Password">
                        </div>
                        <div class="raw100 raw-left rg-row raw-margin-top-24">
                            <label for="new_password">New Password</label>
                            <input id="new_password" type="password" name="new_password" class="form-control" placeholder="New Password">
                        </div>
                        <div class="raw100 raw-left rg-row raw-margin-top-24">
                            <label for="retype_password">New Password</label>
                            <input id="retype_password" type="password" name="retype_password" class="form-control" placeholder="Retype New Password">
                        </div>
                        <div class="raw100 raw-left raw-margin-top-24">
                            <input id="update" type="submit" class="btn btn-primary raw-right" value="Save New Password">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

@stop