<?php

use Illuminate\Routing\Router;

/** @var Router $router */
$router->group(['prefix' =>'/user/all-users'], function (Router $router) {
    $router->get('', [
        'as' => 'admin.user.all-users.index',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.all-users.index',
    ]);

    $router->get('create', [
        'as' => 'admin.user.all-users.create',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.all-users.create',
    ]);

    $router->get('{id}/edit', [
        'as' => 'admin.user.all-users.edit',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.all-users.edit',
    ]);

});
$router->group(['prefix' =>'/invoices'], function (Router $router) {
    $router->get('', [
      'as' => 'admin.invoices.index',
      'uses' => 'InvoiceController@vue',
      'middleware' => 'can:user.invoices.index',
    ]);
});

$router->group(['prefix' =>'/user/admin-users'], function (Router $router) {
    $router->get('', [
        'as' => 'admin.user.admin-users.index',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.admin-users.index',
    ]);

    $router->get('create', [
        'as' => 'admin.user.admin-users.create',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.admin-users.create',
    ]);

    $router->get('{id}/edit', [
        'as' => 'admin.user.admin-users.edit',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.admin-users.edit',
    ]);
});

$router->group(['prefix' =>'/user/traders'], function (Router $router) {
    $router->get('/', [
        'as' => 'admin.user.traders.index',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.traders.index',
    ]);

    $router->get('/{id}', [
        'as' => 'admin.user.traders.show',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.traders.show',
    ]);
});

$router->group(['prefix' =>'/user/experts'], function (Router $router) {
    $router->get('/', [
        'as' => 'admin.user.experts.index',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.experts.index',
    ]);

    $router->get('/{id}', [
        'as' => 'admin.user.experts.show',
        'uses' => 'UserController@vue',
        'middleware' => 'can:user.experts.show',
    ]);

    $router->get('/{id}/edit', [
        'as' => 'admin.user.experts.edit',
        'uses' => 'UserController@vue',
        // 'middleware' => 'can:user.experts.exit',
    ]);
});

$router->group(['prefix' =>'/user/roles'], function (Router $router) {
    $router->get('', [
        'as' => 'admin.user.roles.index',
        'uses' => 'RolesController@index',
        'middleware' => 'can:user.roles.index',
    ]);

    $router->get('roles/create', [
        'as' => 'admin.user.roles.create',
        'uses' => 'RolesController@create',
        'middleware' => 'can:user.roles.create',
    ]);

    $router->post('roles', [
        'as' => 'admin.user.roles.store',
        'uses' => 'RolesController@store',
        'middleware' => 'can:user.roles.create',
    ]);

    $router->get('roles/{id}/edit', [
        'as' => 'admin.user.roles.edit',
        'uses' => 'RolesController@edit',
        'middleware' => 'can:user.roles.edit',
    ]);
});

$router->group(['prefix' => '/account'], function (Router $router) {
    $router->get('profile', [
        'as' => 'admin.account.profile.edit',
        'uses' => 'UserController@vue',
    ]);
});
