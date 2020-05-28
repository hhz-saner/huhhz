<?php

use Illuminate\Routing\Router;

Admin::routes();

$middleware = config('admin.route.middleware');
if (App\Helper\Helper::isWechat()) {
    array_push($middleware, 'wechat.oauth');
}
Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => $middleware,
], function (Router $router) {
    $router->get('auth/getLoginToken', 'AuthController@getLoginToken');
    $router->get('/', 'HomeController@index')->name('admin.home');

    $router->resource('Dns', 'DnsController');

});
