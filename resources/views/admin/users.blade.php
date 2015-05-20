@extends('layouts.standard')

@section('content')

<div class="raw100">
    <h2>Admin : User Manager</h2>
</div>

<div class="raw100 raw-margin-top-24">

    @foreach($users as $user)

        <div class="raw100 raw-left user-row">
            <div class="raw25 raw-left">
                <p><a href="{{ URL::to('admin/editor/'.Crypto::encrypt($user->id)) }}">{{ $user->user_email }}</a></p>
            </div>
            <div class="raw25 raw-left">
                <p>{{ $user->user_role }}</p>
            </div>
            <div class="raw50 raw-left text-right raw-padding-right-8">
                <p>{{ ($user->user_active == "inactive") ? "Not Active" : "Active" }}</p>
            </div>
        </div>

    @endforeach

</div>

@stop