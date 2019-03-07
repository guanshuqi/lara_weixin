<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->get('/weixin/view/sendmsg','WxuserController@sendMsgView');//群发视图
    $router->post('/weixin/sendmsg','WxuserController@sendMsg');//群发消息
});
