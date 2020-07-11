<?php
/**
 * 前台相关路由
 */

// 首页
Route::get('/', 'HomeController@index');
Route::get('/home', 'HomeController@welcome');

// 前台登录注册路由
Auth::routes();

// 前台需要登录验证的路由
Route::group([
	'middleware' => 'auth'
], function () {

});
