<?php
/**
 * 地图管理路由
 */
Route::group([
	'prefix' => 'navigationroad',
	'namespace' => 'NavigationRoad'
], function () {
	//编辑或者新建路线
	Route::get('/route_edit', 'NavigationRoadController@route_edit')->name('admin.navigationroad.route_edit');
	//保存路线
	Route::post('/route_save', 'NavigationRoadController@route_save')->name('admin.navigationroad.route_save');
});