@extends('layouts.standard')

@section('content')

@include('admin.modals')

<div class="raw100">
<a class="btn btn-primary pull-right" href="{!! URL::to('admin/users/create') !!}">Add User</a>
    <h2>Admin : User Manager</h2>
</div>

<table class="table table-striped">

    <thead>
        <th>Email</th>
        <th>Role</th>
        <th class="raw-m-hide">Account State</th>
        <th width="50px">Action</th>
    </thead>

    @foreach($users as $user)

        <tr>
            <td>
                <a href="{{ URL::to('admin/users/editor/'.Crypto::encrypt($user->id)) }}">{{ $user->user_email }}</a>
            </td>
            <td>
                {{ $user->user_role }}
            </td>
            <td class="raw-m-hide">
                {{ ($user->user_active == "inactive") ? "Not Active" : "Active" }}
            </td>
            <td class="text-right">
                <a href="{{ URL::to('admin/users/editor/'.Crypto::encrypt($user->id)) }}"><i class="text-info glyphicon glyphicon-edit"></i></a>
                <a href="#" onclick="confirmDelete('{!! URL::to('admin/users/delete/'.Crypto::encrypt($user->id)) !!}')"><i class="text-danger glyphicon glyphicon-remove"></i></a>
            </td>
        </tr>

    @endforeach

</table>

{!! $users !!}

{!! Minify::javascript('/js/accounts/admin.js') !!}

@stop