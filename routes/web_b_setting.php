<?php
Route::group([
	'prefix' => 'setting',
	'namespace' => 'Setting'
], function () {
	Route::group([
		'prefix' => 'adminusers',
	], function () {
		// 管理员列表
		Route::get('/', 'AdminUsersController@index')->name('admin.setting.adminusers');
		// 添加管理员
		Route::get('/add', 'AdminUsersController@add')->name('admin.setting.adminusers.add');
		// 编辑管理员
		Route::get('/edit/{uid}', 'AdminUsersController@edit')->name('admin.setting.adminusers.edit');
		// 保存用户信息
		Route::post('/save', 'AdminUsersController@save')->name('admin.setting.adminusers.save');
		// 删除管理员
		Route::get('/delete/{uid}', 'AdminUsersController@delete')->name('admin.setting.adminusers.delete');
		// 管理员修改账户信息
		Route::match([
			'get',
			'post'
		], '/edit_userinfo', 'AdminUsersController@edit_userinfo')->name('admin.setting.adminusers.edit_userinfo');
	});

	Route::group([
		'prefix' => 'admingroup',
	], function () {
		// 管理员用户组列表
		Route::get('/', 'AdminGroupController@index')->name('admin.setting.admingroup');
		// 添加用户组
		Route::get('/add', 'AdminGroupController@add')->name('admin.setting.admingroup.add');
		// 编辑用户组
		Route::get('/edit/{groupid}', 'AdminGroupController@edit')->name('admin.setting.admingroup.edit');
		// 保存用户组
		Route::post('/save', 'AdminGroupController@save')->name('admin.setting.admingroup.save');
		// 删除用户组
		Route::get('/delete/{groupid}', 'AdminGroupController@delete')->name('admin.setting.admingroup.delete');
	});

	// 管理员登录日志
	Route::get('adminloginlog', 'AdminLoginLogController@index')->name('admin.setting.adminloginlog');

	// 网站设置
	Route::match([
		'get',
		'post'
	], '/basesetting', 'BaseSettingController@index')->name('admin.setting.basesetting');

	Route::group([
		'prefix' => 'systemlog',
	], function () {
		// 系统日志查看
		Route::get('/', 'SystemlogController@index')->name('admin.setting.systemlog');
		// 取得目录明细
		Route::get('/getdir', 'SystemlogController@getdir')->name('admin.setting.systemlog.getdir');
		// 查看文件
		Route::get('/view', 'SystemlogController@view')->name('admin.setting.systemlog.view');
		// 下载文件
		Route::get('/download', 'SystemlogController@download')->name('admin.setting.systemlog.download');
	});
});
