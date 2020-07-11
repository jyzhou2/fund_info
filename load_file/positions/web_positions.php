<?php
Route::group([
	'prefix' => 'positions',
	'namespace' => 'Positions'
], function () {
	//定位管理
	Route::group([
	], function () {
		// 定位列表
		Route::get('/positions_list/{map_id?}/{x?}/{y?}/{auto_num?}/{keywords?}', 'PositionsController@positions_list')->name('admin.positions.positions_list');
		//ajax获取地图定位信息
		Route::post('/ajax_map', 'PositionsController@ajax_map')->name('admin.positions.ajax_map');
		//人员查询
		Route::post('/search', 'PositionsController@search')->name('admin.positions.search');
		//点位详情查看
		Route::post('/point', 'PositionsController@point')->name('admin.positions.point');

	});
	//轨迹管理
	Route::group([
	], function () {
		//租赁中的轨迹列表
		Route::get('/rent_trajectory_list', 'TrajectoryController@rent_trajectory_list')->name('admin.positions.rent_trajectory_list');
		//租赁中的轨迹详情
		Route::get('/rent_trajectory_info/{rent_id}/{map_id?}', 'TrajectoryController@rent_trajectory_info')->name('admin.positions.rent_trajectory_info');

		//已归还的轨迹列表
		Route::get('/backup_trajectory_list', 'TrajectoryController@backup_trajectory_list')->name('admin.positions.backup_trajectory_list');
		//已归还的轨迹详情
		Route::get('/backup_trajectory_info/{rent_id}/{map_id?}', 'TrajectoryController@backup_trajectory_info')->name('admin.positions.backup_trajectory_info');

		//用户轨迹列表
		Route::get('/user_trajectory_list', 'TrajectoryController@user_trajectory_list')->name('admin.positions.user_trajectory_list');
		//用户轨迹详情
		Route::get('/user_trajectory_info/{uid}/{look_date}/{map_id?}', 'TrajectoryController@user_trajectory_info')->name('admin.positions.user_trajectory_info');

	});
});
