<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
Route::get('/weixin/token','Weixin\WxController@reAccessToken');//获取access_token
Route::get('/weixin/createMenu','Weixin\WxController@createMenu');//创建菜单
Route::get('/weixin/getUserInfo','Weixin\WxController@getUserInfo');//获取用户信息
Route::get('/weixin/wxEvent','Weixin\WxController@valid');//首次接入
Route::post('/weixin/wxEvent','Weixin\WxController@wxEvent');//接收微信推送事件


