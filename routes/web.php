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

$router->get('/', function(){return 'Bitsky © 2019';});
$router->post('/init', 'DeviceController@init');
$router->post('/link', 'LinkController@create');
$router->post('/checkLink', 'LinkController@check');
$router->post('/activeLink', 'LinkController@activeLink');