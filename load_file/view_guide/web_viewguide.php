<?php
Route::group([
	'prefix' => 'viewguide',
	'namespace' => 'ViewGuide'
], function () {
	//实景导览列表
	Route::get('/view_guide_list', 'ViewGuideController@view_guide_list')->name('admin.viewguide.view_guide_list');
	// 实景导览编辑
	Route::match([
		'get',
		'post'
	],'/view_guide_edit/{id}', 'ViewGuideController@view_guide_edit')->name('admin.viewguide.view_guide_edit');
	// 实景导览删除
	Route::get('/view_guide_delete/{id}', 'ViewGuideController@view_guide_delete')->name('admin.viewguide.view_guide_delete');
	//资源打包更新
	Route::match([
		'get',
		'post'
	], '/resource_zip', 'ViewGuideController@resource_zip')->name('admin.viewguide.resource_zip');
	Route::post('/update_zip', 'ViewGuideController@update_zip')->name('admin.viewguide.update_zip');
	Route::post('/end_zip', 'ViewGuideController@end_zip')->name('admin.viewguide.end_zip');
	//资源文件下载
	Route::get('/down_file', 'ViewGuideController@down_file')->name('admin.viewguide.down_file');
});