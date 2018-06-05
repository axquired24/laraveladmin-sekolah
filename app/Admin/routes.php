<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');

    $router->resource('sekolah', SekolahCtrl::class);
    $router->resource('kelas', KelasCtrl::class);
    $router->resource('siswa', SiswaCtrl::class);

});
