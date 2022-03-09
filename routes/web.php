<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->get('/roles/all', 'RoleController@findAll');
$router->get('/roles/by-id/{id}', 'RoleController@findById');

$router->get('/statuses/all', 'StatusController@findAll');
$router->get('/statuses/by-id/{id}', 'StatusController@findById');

$router->post('/auth/login', 'AuthController@login');
$router->post('/auth/register', 'AuthController@register');
$router->get('/auth/confirm-password/{uuid}', 'AuthController@confirm');
$router->get('/auth/regenerate-token/{uuid}', 'AuthController@regenerate');
$router->post('/auth/reset-password', 'AuthController@reset');

$router->group(['middleware' => 'auth'], function () use ($router) {
    $router->post('/user/update-email', 'UserController@updateEmail');
});

$router->post('/api/logs', 'LogsApiAuthController@auth');
