<?php
//随手怕列表
Route::get('pai_list', 'PaiController@pai_list');
//随手怕评论列表
Route::get('pai_comment_list', 'PaiController@pai_comment_list');
Route::group([
	'middleware' => 'auth:api'
], function () {
	//随手拍图片上传
	Route::post('pai_uploadimg', 'PaiController@pai_uploadimg');
	//微信图片上传
	Route::post('wx_upload_img', 'PaiController@wx_upload_img');
	//随手拍发布
	Route::post('send_pai', 'PaiController@send_pai');
	//评论随手拍
	Route::post('pai_comment', 'PaiController@pai_comment');
	//随手拍评论点赞取消
	Route::get('pai_dolike', 'PaiController@pai_dolike');
	//我的随手怕列表
	Route::get('my_pai_list', 'PaiController@my_pai_list');
	//我的随手怕删除接口
	Route::get('del_my_pai_list', 'PaiController@del_my_pai_list');
});
