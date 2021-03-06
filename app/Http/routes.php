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

$app->get('/', function () use ($app) {
    $version = config('app.version');
    return "Measurement API v{$version}";
});

$app->group(['prefix' => 'v1', 'namespace' => 'App\Http\Controllers', 'middleware' => 'auth'], function ($app) {
    $app->get('device/', 'DeviceController@index');
    $app->post('device/', 'DeviceController@store');
    $app->get('device/{id}', ['as' => 'device.show', 'uses' => 'DeviceController@show']);
    $app->put('device/{id}', 'DeviceController@update');
    $app->get('device/{id}/measurements', 'DeviceController@measurements');
    $app->delete('device/{id}', 'DeviceController@destroy');

    $app->get('measurement/', 'MeasurementController@index');
    $app->post('measurement/', 'MeasurementController@store');
    $app->get('measurement/{id}', ['as' => 'measurement.show', 'uses' => 'MeasurementController@show']);
    $app->put('measurement/{id}', 'MeasurementController@update');
    $app->get('measurement/{id}/device', 'MeasurementController@device');
    $app->delete('measurement/{id}', 'MeasurementController@destroy');
});

$app->group(['prefix' => 'web', 'namespace' => 'App\Http\Controllers'], function ($app) {
    $app->get('/setup', ['as' => 'web.setup', 'uses' => 'WebController@setup']);
    $app->post('/setup', 'WebController@postSetup');
});