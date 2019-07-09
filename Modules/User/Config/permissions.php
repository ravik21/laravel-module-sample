<?php

return [
    'user.device-token' => [
        'register' => 'user::users.device-tokens register resource',
    ],
    'user.profile' => [
        'edit' => 'user::users.profile edit resource',
    ],
    'user.all-users' => [
        'index' => 'user::users.list user',
        'show' => 'user::users.show user',
        'create' => 'user::users.create user',
        'edit' => 'user::users.edit user',
        'roles' => 'user::roles.list resource',
        'permissions' => 'user::users.permissions list resource',
    ],
    'user.admin-users' => [
        'index' => 'user::users.list user',
        'show' => 'user::users.show user',
        'create' => 'user::users.create user',
        'edit' => 'user::users.edit user',
        'roles' => 'user::roles.list resource',
    ],
    'user.users' => [
        'index' => 'user::users.list user',
        'show' => 'user::users.show user',
        'destroy' => 'user::users.destroy user',
        'suspend' => 'user::users.suspend user',
        'unsuspend' => 'user::users.unsuspend user',
        'resetPassword' => 'user::users.resetPassword user',
        'sendInvite' => 'user::users.sendInvite user',
    ],
    'user.account.profile' => [
        'show' => 'user::users.profile show resource',
        'edit' => 'user::users.profile edit resource',
    ]
];
