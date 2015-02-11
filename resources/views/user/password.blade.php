@section('content')

<div class="raw100 text-center">
    <h2>Settings: Password</h2>
</div>

<div class="raw-device raw-margin-auto raw-margin-top-24">
    <form id="userSettings" method="post" action="{{ URL::to('user/settings/update/password') }}">
        <?= Form::token(); ?>
        <div class="raw100 raw-left rg-row raw-margin-top-24">
            <div class="rg-col-6">
                <label for="old_password" class="raw-margin-top-8 raw-right">Old Password</label>
            </div>
            <div class="rg-col-6">
                <input id="old_password" type="password" name="old_password" class="form-control" placeholder="Old Password">
            </div>
        </div>
        <div class="raw100 raw-left rg-row raw-margin-top-24">
            <div class="rg-col-6">
                <label for="new_password" class="raw-margin-top-8 raw-right">New Password</label>
            </div>
            <div class="rg-col-6">
                <input id="new_password" type="password" name="new_password" class="form-control" placeholder="New Password">
            </div>
        </div>
        <div class="raw100 raw-left rg-row raw-margin-top-24">
            <div class="rg-col-6">
                <label for="retype_password" class="raw-margin-top-8 raw-right">New Password</label>
            </div>
            <div class="rg-col-6">
                <input id="retype_password" type="password" name="retype_password" class="form-control" placeholder="Retype New Password">
            </div>
        </div>
        <div class="raw100 raw-left rg-row raw-margin-top-24">
            <input id="update" type="submit" class="btn btn-primary raw-right" value="Save New Password">
        </div>
    </form>
</div>

@stop