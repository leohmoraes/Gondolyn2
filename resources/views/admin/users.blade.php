@extends('layouts.standard')

@section('content')

@include('admin.modals')

<div class="raw100">
    <h2>Admin : User Manager</h2>
</div>

<table class="table table-striped">

    <thead>
        <th>Email</th>
        <th>Role</th>
        <th>Account State</th>
        <th width="50px">Action</th>
    </thead>

    @foreach($users as $user)

        <tr>
            <td>
                <a href="{{ URL::to('admin/editor/'.Crypto::encrypt($user->id)) }}">{{ $user->user_email }}</a>
            </td>
            <td>
                {{ $user->user_role }}
            </td>
            <td>
                {{ ($user->user_active == "inactive") ? "Not Active" : "Active" }}
            </td>
            <td>
                <a href="{{ URL::to('admin/editor/'.Crypto::encrypt($user->id)) }}"><i class="text-info glyphicon glyphicon-edit"></i></a>
                <a href="#" onclick="confirmDelete('{!! URL::to('admin/delete/user/'.Crypto::encrypt($user->id)) !!}')"><i class="text-danger glyphicon glyphicon-remove"></i></a>
            </td>
        </tr>

    @endforeach

</table>

{!! Minify::javascript('/js/accounts/admin.js') !!}

@stop