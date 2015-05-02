<?php

/*
|--------------------------------------------------------------------------
| Permission Matix
|--------------------------------------------------------------------------
|
| Within the permissions matrix we can set the roles of the app as well
| as the groups of roles. This will enable easy modifications of what
| user types have access to what without having to create complicated
| arrays in routes. Groups can only be single level arrays.
|
| We do this routes with:
|
| 'permission' => 'role OR groups.groupName'
|
*/

return array(

    // Default Role
    'default_role' => 'member',

    'roles' => [
        'admin',
        'member',
    ],

    'groups' => [
        'all' => [
            'admin',
            'member'
        ],
    ],

);
