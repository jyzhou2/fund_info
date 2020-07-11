<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/8
 * Time: 15:10
 */
Route::group([
	'prefix' => 'load',
	'namespace' => 'Load'
], function () {
	// 模块装载列表
	Route::get('/load_list', 'LoadController@load_list')->name('admin.load.load_list');
	Route::get('/install/{key}', 'LoadController@install')->name('admin.load.install');
	Route::get('/uninstall/{key}', 'LoadController@uninstall')->name('admin.load.uninstall');
	Route::get('/uninstall_controller', 'LoadController@uninstall_controller')->name('admin.load.uninstall_controller');
});