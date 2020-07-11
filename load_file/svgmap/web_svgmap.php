<?php
/**
 * 地图管理路由
 */
Route::group([
	'prefix' => 'svgmap',
	'namespace' => 'SvgMapAdmin'
], function () {
	// 用户管理列表
	Route::get('/list', 'SvgMapAdminController@map_list')->name('admin.svgmap.svgmap_list');
	// 地图编辑
	Route::match([
		'get',
		'post'
	], '/edit/{id}', 'SvgMapAdminController@edit')->name('admin.svgmap.edit');
	// 地图删除
	Route::get('/delete/{id}', 'SvgMapAdminController@delete')->name('admin.svgmap.delete');
	// 地图预览
	Route::get('/view/{id}', 'SvgMapAdminController@view')->name('admin.svgmap.view');
});