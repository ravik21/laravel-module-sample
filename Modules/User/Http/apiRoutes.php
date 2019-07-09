<?php

use Illuminate\Routing\Router;
/** @var Router $router */

$router->group(['prefix' =>'/traders', 'middleware' => ['api.key', 'cors']], function (Router $router) {
    $router->post('register', [
        'as'    => 'api.user.traders.register',
        'uses'  => 'TraderController@store',
    ]);
});

$router->group(['prefix' =>'/traders', 'middleware' => ['api.key', 'api.token', 'cors']], function (Router $router) {
    $router->post('invite', [
        'as'          => 'api.user.traders.invite',
        'uses'        => 'TraderInviteController@store',
        'middleware'  => 'token-can:user.traders.invite'
    ]);

    $router->get('{user}', [
        'as'          => 'api.user.admin.traders.show',
        'uses'        => 'TraderController@show',
        'middleware'  => 'token-can:user.traders.show',
    ]);
});

$router->group(['prefix' =>'/experts', 'middleware' => ['api.key', 'cors']], function (Router $router) {
    $router->post('register', [
        'as'    => 'api.user.experts.register',
        'uses'  => 'ExpertController@store',
    ]);

    $router->get('/', [
        'as'   => 'api.user.experts.index',
        'uses' => 'ExpertController@index',
    ]);

    $router->get('{user}', [
        'as'          => 'api.user.experts.show',
        'uses'        => 'ExpertController@show',
        'middleware'  => 'token-can:user.experts.show',
    ]);

    $router->post('invite/check-code', [
        'as'    => 'api.user.experts.invite.check-code',
        'uses'  => 'ExpertInviteController@index',
    ]);

    $router->post('marked-as-favourite', [
        'as'          => 'api.user.experts.mark-as-favourite',
        'uses'        => 'ExpertController@markedAsFavourite',
    ]);

    $router->post('mark-as-favourite', [
        'as'          => 'api.user.experts.mark-as-favourite',
        'uses'        => 'ExpertController@markAsFavourite',
        'middleware'  => 'token-can:user.expert.mark-as-favourite'
    ]);

    $router->post('unmark-as-favourite', [
        'as'          => 'api.user.experts.unmark-as-favourite',
        'uses'        => 'ExpertController@unMarkAsFavourite',
        'middleware'  => 'token-can:user.expert.unmark-as-favourite'
    ]);
});

$router->group(['prefix' =>'/auth', 'middleware' => ['api.key', 'cors']], function (Router $router) {
    $router->post('pusher', [
      'as'          => 'api.user.auth.pusher',
      'uses'        => 'UserSessionController@authenticatePusher',
      'middleware'  => 'token-can:user.auth.pusher'
    ]);

    $router->post('status', [
      'as'          => 'api.user.auth.status',
      'uses'        => 'UserSessionController@updateStatus',
      'middleware'  => 'token-can:user.auth.status'
    ]);
});

$router->group(['prefix' =>'/users', 'middleware' => ['api.key', 'cors']], function (Router $router) {
    $router->post('resend-verification', [
        'as'    => 'api.user.resend-verification',
        'uses'  => 'UserController@resendVerificationEmail',
    ]);
    $router->post('login', [
        'as'    => 'api.user.login',
        'uses'  => 'UserSessionController@store',
    ]);
    $router->post('validate-token', [
        'as'    => 'api.user.validate-token',
        'uses'  => 'UserSessionController@validate',
    ]);
    $router->post('forgotten-password', [
        'as'    => 'api.user.forgotten-password',
        'uses'  => 'ForgottenPasswordController@store',
    ]);
    $router->get('register/validate-vat', [
        'as'    => 'api.user.register.validateVat',
        'uses'  => 'UserController@validateVat'
    ]);
});

$router->group(['prefix' =>'/chat', 'middleware' => ['api.key', 'api.token', 'cors']], function (Router $router) {

  $router->group(['prefix' =>'/user'], function (Router $router) {

    $router->get('/', [
      'as'          => 'api.user.chat.users',
      'uses'        => 'ChatMessageController@users',
      'middleware'  => 'token-can:user.chat.users'
    ]);

    $router->post('/is-typing', [
      'as'          => 'api.user.chat.typing',
      'uses'        => 'ChatMessageController@userIsTyping'
    ]);

  });

  $router->group(['prefix' =>'/messages'], function (Router $router) {

    $router->get('/', [
      'as'          => 'api.user.chat.messages',
      'uses'        => 'ChatMessageController@messages',
      'middleware'  => 'token-can:user.chat.messages'
    ]);

    $router->get('/notification', [
      'as'          => 'api.user.chat.messages.notification',
      'uses'        => 'ChatMessageController@messagesNotification'
    ]);

    $router->post('/', [
      'as'          => 'api.user.chat.store',
      'uses'        => 'ChatMessageController@store',
      'middleware'  => 'token-can:user.chat.store'
    ]);

    $router->post('/broadcast', [
      'as'          => 'api.user.chat.broadcast',
      'uses'        => 'ChatMessageController@broadcast',
      'middleware'  => 'token-can:user.chat.broadcast'
    ]);

  });

});

$router->group(['prefix' =>'/users', 'middleware' => ['api.key', 'api.token', 'cors']], function (Router $router) {
    $router->get('profile', [
        'as'          => 'api.user.profile.show',
        'uses'        => 'ProfileController@show',
        'middleware'  => 'token-can:user.profile.show'
    ]);

    $router->post('profile', [
        'as'          => 'api.user.profile.update',
        'uses'        => 'ProfileController@update',
        'middleware'  => 'token-can:user.profile.edit'
    ]);

    $router->post('/profile/avatar', [
        'as'          => 'api.user.profile.update-avatar',
        'uses'        => 'ProfileController@updateAvatar',
        'middleware'  => 'token-can:user.profile.edit'
    ]);

    $router->post('/profile/terms-conditions', [
        'as'          => 'api.user.profile.terms-conditions',
        'uses'        => 'ProfileController@uploadTermsConditions',
        'middleware'  => 'token-can:user.profile.upload-terms-conditions'
    ]);
    $router->delete('/profile/delete-terms-conditions', [
        'as'          => 'api.user.profile.delete-terms-conditions',
        'uses'        => 'ProfileController@deleteTermsConditions',
        'middleware'  => 'token-can:user.profile.delete-terms-conditions'
    ]);

    $router->post('logout', [
        'as'          => 'api.user.logout',
        'uses'        => 'UserSessionController@destroy',
        'middleware'  => 'token-can:user.users.logout',
    ]);

    $router->get('tags', [
        'as'    => 'api.user.tags',
        'uses'  => 'TagController@index'
    ]);

    $router->post('tags', [
        'as'    => 'api.user.update-tags',
        'uses'  => 'TagController@store'
    ]);

});

$router->group(['prefix' => 'admin/user', 'middleware' => ['api.key', 'api.token', 'auth.admin']], function (Router $router) {

    $router->get('groups', [
        'as' => 'api.user.admin.groups',
        'uses' => 'Admin\GroupController@index',
        'middleware' => 'token-can:group.groups.user.index',
    ]);
    $router->get('tags', [
      'as' => 'api.user.admin.tags',
      'uses' => 'Admin\TagController@index',
      'middleware' => 'token-can:taggable.tags.user.index',
    ]);
});

$router->group(['prefix' =>'/admin', 'middleware' => ['api.key', 'api.token', 'auth.admin']], function (Router $router) {
    $router->bind('user', function ($id) {
        return app(\Modules\User\Repositories\UserRepository::class)->find($id);
    });


    $router->get('all-users', [
        'as'          => 'api.user.admin.all-users',
        'uses'        => 'Admin\MainController@index',
        'middleware'  => 'token-can:user.all-users.index',
    ]);

    $router->get('all-users/{user}', [
        'as'          => 'api.user.admin.all-users.show',
        'uses'        => 'Admin\MainController@show',
        'middleware'  => 'token-can:user.all-users.show',
    ]);

    $router->post('all-users', [
        'as'          => 'api.user.admin.all-users.store',
        'uses'        => 'Admin\MainController@store',
        'middleware'  => 'token-can:user.all-users.create',
    ]);

    $router->post('all-users/{user}/update', [
        'as'          => 'api.user.admin.all-users.update',
        'uses'        => 'Admin\MainController@update',
        'middleware'  => 'token-can:user.all-users.edit',
    ]);

    $router->get('admin-users', [
        'as'          => 'api.user.admin.admin-users',
        'uses'        => 'Admin\AdminController@index',
        'middleware'  => 'token-can:user.admin-users.index',
    ]);

    $router->get('admin-users/roles', [
        'as'          => 'api.user.admin.admin-users.roles',
        'uses'        => 'Admin\RoleController@getAdminRoles',
        'middleware'  => 'token-can:user.admin-users.roles',
    ]);

    $router->get('admin-users/{user}', [
        'as'          => 'api.user.admin.admin-users.show',
        'uses'        => 'Admin\AdminController@show',
        'middleware'  => 'token-can:user.admin-users.show',
    ]);

    $router->post('admin-users', [
        'as'          => 'api.user.admin.admin-users.store',
        'uses'        => 'Admin\AdminController@store',
        'middleware'  => 'token-can:user.admin-users.create',
    ]);

    $router->post('admin-users/{user}/update', [
        'as'          => 'api.user.admin.admin-users.update',
        'uses'        => 'Admin\AdminController@update',
        'middleware'  => 'token-can:user.admin-users.edit',
    ]);

    $router->get('experts', [
        'as'          => 'api.user.admin.experts.index',
        'uses'        => 'Admin\ExpertController@index',
        'middleware'  => 'token-can:user.experts.index',
    ]);

    $router->post('experts/invite', [
        'as'          => 'api.user.admin.experts.invite',
        'uses'        => 'Admin\ExpertInviteController@store',
        'middleware'  => 'token-can:user.experts.invite',
    ]);

    $router->get('experts/{user}/accept', [
        'as'          => 'api.user.admin.experts.accept',
        'uses'        => 'Admin\ExpertController@markApprovalAsAccepted',
        'middleware'  => 'token-can:user.experts.accept',
    ]);

    $router->get('experts/{user}/reject', [
        'as'          => 'api.user.admin.experts.reject',
        'uses'        => 'Admin\ExpertController@markApprovalAsRejected',
        'middleware'  => 'token-can:user.experts.reject',
    ]);

    $router->get('traders', [
        'as'          => 'api.user.admin.traders.index',
        'uses'        => 'Admin\TraderController@index',
        'middleware'  => 'token-can:user.traders.index',
    ]);

    $router->get('traders/{user}', [
        'as'          => 'api.user.admin.traders.show',
        'uses'        => 'Admin\TraderController@show',
        'middleware'  => 'token-can:user.traders.show',
    ]);

    $router->get('traders/{user}/accept', [
        'as'          => 'api.user.admin.traders.accept',
        'uses'        => 'Admin\TraderController@markApprovalAsAccepted',
        'middleware'  => 'token-can:user.traders.accept',
    ]);

    $router->get('traders/{user}/reject', [
        'as' => 'api.user.admin.traders.reject',
        'uses' => 'Admin\TraderController@markApprovalAsRejected',
        'middleware' => 'token-can:user.traders.reject',
    ]);

    $router->delete('users/{user}/delete', [
        'as' => 'api.user.admin.users.destroy',
        'uses' => 'Admin\UserController@destroy',
        'middleware' => 'token-can:user.users.destroy',
    ]);

    $router->get('users/{user}/suspend', [
        'as' => 'api.user.admin.users.suspend',
        'uses' => 'Admin\UserController@suspend',
        'middleware' => 'token-can:user.users.suspend',
    ]);

    $router->get('users/{user}/unsuspend', [
        'as' => 'api.user.admin.users.unsuspend',
        'uses' => 'Admin\UserController@unsuspend',
        'middleware' => 'token-can:user.users.unsuspend',
    ]);

    $router->get('users/{user}/activate', [
        'as' => 'api.user.admin.users.activate',
        'uses' => 'Admin\UserController@activate',
        'middleware' => 'token-can:user.users.activate',
    ]);

    $router->post('users/invite', [
        'as' => 'api.user.admin.users.invite',
        'uses' => 'Admin\UserController@sendInvite',
        'middleware' => 'token-can:user.users.invite',
    ]);

    $router->post('users/nudge', [
        'as' => 'api.user.admin.users.nudge',
        'uses' => 'Admin\NudgeController@store',
        'middleware' => 'token-can:user.users.nudge',
    ]);

    $router->post('users/{user}/send-reset-password', [
        'as' => 'api.user.admin.users.resetPassword',
        'uses' => 'Admin\UserController@sendResetPassword',
        'middleware' => 'token-can:user.users.resetPassword',
    ]);

    $router->get('invoices', [
        'as'          => 'api.user.admin.invoices.index',
        'uses'        => 'Admin\InvoiceController@index',
        'middleware'  => 'token-can:user.invoices.index',
    ]);

    $router->get('experts/{user}', [
        'as'          => 'api.user.admin.experts.show',
        'uses'        => 'Admin\ExpertController@show',
        'middleware'  => 'token-can:user.experts.show',
    ]);

    $router->get('experts/{user}/edit', [
        'as'          => 'api.user.admin.experts.edit',
        'uses'        => 'Admin\ExpertController@show',
        'middleware'  => 'token-can:user.experts.edit',
    ]);

    $router->post('experts/{user}/store', [
        'as'          => 'api.user.admin.experts.update',
        'uses'        => 'Admin\ExpertController@update',
        'middleware'  => 'token-can:user.experts.store',
    ]);


    $router->group(['prefix' => 'roles'], function (Router $router) {
        $router->bind('role', function ($id) {
            return app(\Modules\User\Repositories\RoleRepository::class)->find($id);
        });

        $router->get('/', [
            'as' => 'api.user.admin.roles',
            'uses' => 'Admin\RoleController@index',
            'middleware' => 'token-can:user.roles.index',
        ]);

        $router->post('/', [
            'as' => 'api.user.admin.roles.store',
            'uses' => 'Admin\RoleController@store',
            'middleware' => 'token-can:user.roles.create',
        ]);

        $router->get('/{role}', [
            'as' => 'api.user.admin.roles.show',
            'uses' => 'Admin\RoleController@show',
            'middleware' => 'token-can:article.articles.show',
        ]);

        $router->post('{role}/update', [
            'as' => 'api.user.admin.roles.update',
            'uses' => 'Admin\RoleController@update',
            'middleware' => 'token-can:user.roles.edit',
        ]);

        $router->delete('{role}/delete', [
            'as' => 'api.user.admin.roles.destroy',
            'uses' => 'Admin\RoleController@destroy',
            'middleware' => 'token-can:user.roles.destroy',
        ]);
    });

    $router->get('permissions', [
        'as' => 'api.user.admin.permissions',
        'uses' => 'Admin\PermissionController@index',
        'middleware' => 'token-can:user.permissions.index',
    ]);

    $router->get('permissions/all', [
      'as'          => 'api.user.admin.permissions.index',
      'uses'        => 'Admin\MainController@permissions'
    ]);
});

$router->group(['prefix' =>'/admin/profile', 'middleware' => ['api.key', 'api.token', 'auth.admin']], function (Router $router) {
    $router->get('/', [
        'as' => 'api.account.admin.profile.show',
        'uses' => 'Admin\ProfileController@show',
        'middleware' => 'token-can:user.account.profile.show'
    ]);

    $router->post('/', [
        'as' => 'api.account.admin.profile.update',
        'uses' => 'Admin\ProfileController@update',
        'middleware' => 'token-can:user.account.profile.edit'
    ]);

    $router->post('/avatar', [
        'as' => 'api.account.admin.profile.avatar',
        'uses' => 'Admin\ProfileController@updateAvatar',
        'middleware' => 'token-can:user.account.profile.edit'
    ]);
});
