<?php

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

$router->group(['prefix' => '/api/products'], function () use ($router) {
	$router->post('/', 'ProductController@store');
	$router->get('/{id}', 'ProductController@show');
	$router->get('/', 'ProductController@index');
	$router->put('/{id}', 'ProductController@update');
	$router->delete('/{id}', 'ProductController@destroy');
});

$router->group(['prefix' => '/api/imports'], function () use ($router) {
	$router->get('/', 'ImportController@index');
	$router->get('/excel2import', 'ImportController@excel2import');
	$router->get('/import2product', 'ImportController@import2product');
	$router->get('/execqueue', 'ImportController@execqueue');
	$router->get('/importall', 'ImportController@importall');
});
