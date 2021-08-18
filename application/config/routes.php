<?php

return [
    // site routes
    'page/<view>' => 'site/page',

    // users
    'profile' => 'profile/index',
    'profile/<username>' => 'profile/view',

    // auth and signup
    'login' => 'security/login',
    'signup' => 'registration/register',
    'auth/<authclient>' => 'security/auth',

    // dashboard
    'dashboard' => 'dashboard/index',

    // directory
    'browse' => 'directory/index',

    // photo manager
    'manage' => 'photo-manage/index',
    'upload' => 'photo-manage/upload',

    // like manager
    'connections/likes/<type:(from-you|to-you|mutual)>' => 'connections/likes',
    'connections/likes' => 'connections/likes',

    // messages manager
    'messages' => 'messages/index',

    // block
    'block' => 'block/create',
    'unblock' => 'block/delete',

    // notifications
    'notifications' => 'notifications/index',

    env('ADMIN_PREFIX') => env('ADMIN_PREFIX') . '/default/index',
];
