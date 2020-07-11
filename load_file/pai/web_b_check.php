<?php
/**
 * 审核管理路由
 */
Route::group([
	'prefix' => 'paicheck',
	'namespace' => 'PaiCheck'
], function () {
	// 随手拍审核列表
	Route::get('/pai_list', 'PaiCheckController@pai_list')->name('admin.paicheck.pai_list');
	// 随手拍评论审核列表
	Route::get('/pai_comment_list', 'PaiCheckController@pai_comment_list')->name('admin.paicheck.pai_comment_list');
	// 通过审核
	Route::get('/pass_check/{type}/{ids}', 'PaiCheckController@pass_check')->name('admin.paicheck.pass_check');
	// 审核不通过
	Route::get('/unpass_check/{type}/{ids}', 'PaiCheckController@unpass_check')->name('admin.paicheck.unpass_check');
	// 删除
	Route::get('/del_check/{type}/{ids}', 'PaiCheckController@del_check')->name('admin.paicheck.del_check');
});