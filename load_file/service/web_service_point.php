<?php
Route::group([
	'prefix' => 'servicepoint',
	'namespace' => 'ServicePoint'
], function () {
	//服务设施列表
	Route::get('/service_point_list', 'ServicePointController@service_point_list')->name('admin.servicepoint.service_point_list');
	// 服务设施编辑
	Route::match([
		'get',
		'post'
	],'/service_point_edit/{id}', 'ServicePointController@service_point_edit')->name('admin.servicepoint.service_point_edit');
	// 服务设施删除
	Route::get('/service_point_delete/{id}', 'ServicePointController@service_point_delete')->name('admin.servicepoint.service_point_delete');
});