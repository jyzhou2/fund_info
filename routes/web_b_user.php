<?php
Route::group([
	'prefix' => 'user',
	'namespace' => 'User'
], function () {
	Route::group([
		'prefix' => 'users',
	], function () {
		// 用户管理列表
		Route::get('/', 'UsersController@index')->name('admin.user.users');
		// 添加用户
		Route::get('/add', 'UsersController@add')->name('admin.user.users.add');
		// 编辑用户
		Route::get('/edit/{uid}', 'UsersController@edit')->name('admin.user.users.edit');
		// 保存用户信息
		Route::post('/save', 'UsersController@save')->name('admin.user.users.save');
		// 删除用户
		Route::get('/delete/{uid}', 'UsersController@delete')->name('admin.user.users.delete');
	});
});