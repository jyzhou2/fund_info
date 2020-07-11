<?php
// 意见反馈提交接口
Route::post('feedback/feedback_save', 'FeedbackController@feedback_save');
Route::post('feedback/feedback_img', 'FeedbackController@feedback_img');

Route::group([
	'middleware' => 'auth:api'
], function () {
	// 我的反馈
	Route::get('feedback/my_feedback', 'FeedbackController@my_feedback');
	// 反馈已读
	Route::post('feedback/read_my_feedback', 'FeedbackController@read_my_feedback');
});