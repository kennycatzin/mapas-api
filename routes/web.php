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

$router->get('/key', function() {
    return \Illuminate\Support\Str::random(32);
});


$router->group(['prefix' => 'api'], function () use ($router) {
    // Matches "/api/register
   $router->post('register', 'AuthController@register');
     // Matches "/api/login
    $router->post('login', 'AuthController@login');
    $router->get('renovar-token', 'AuthController@renovarToken');

    $router->post('store-viaje', 'ViajeController@guardarViaje');
    $router->get('get-viajes/{id}', 'ViajeController@getViajesDiarios');
    $router->get('get-ultimo-viaje/{id_viaje}', 'ViajeController@getUltimoViaje');
    $router->get('aceptar-viaje/{id_viaje}', 'ViajeController@aceptarViaje');
    $router->get('rechazar-viaje/{id_viaje}', 'ViajeController@rechazarViaje');
    $router->get('get-estatus-viaje/{id_viaje}', 'ViajeController@getEstatusPago');
    $router->get('get-viaje-completado/{id_viaje}', 'ViajeController@getViajeCompletado');

    

    $router->get('get-tarifas', 'TarifaController@getTarifas');

    $router->post('store-mensaje', 'MensajeController@storeMensaje');
    $router->get('mensaje-visto/{id}', 'MensajeController@mensajeVisto');
    $router->get('get-detalle-mensaje/{id}', 'MensajeController@getDetalleMensaje');
    $router->get('get-mensajes/{id}', 'MensajeController@getMensajes');
    $router->get('get-mensajes-nuevos/{id}', 'MensajeController@getMensajesNuevos');
});