<?php
Route::group([
	'prefix' => 'question',
	'namespace' => 'Question'
], function () {
	// 问卷调查首页
	Route::get('/ques_list', 'QuestionController@ques_list')->name('admin.question.ques_list');
	//问卷调查更改状态
	Route::get('/ques_status', 'QuestionController@ques_status')->name('admin.question.ques_status');
	//获取详情
	Route::post('/ajax_ques', 'QuestionController@ajax_ques')->name('admin.question.ajax_ques');
	//编辑题目
	Route::match(['get','post'],'/edit_ques', 'QuestionController@edit_ques')->name('admin.question.edit_ques');
	Route::match(['get','post'],'/quesinfo_list', 'QuestionController@quesinfo_list')->name('admin.question.quesinfo_list');
	//ajax显示所有题目
	Route::match(['get','post'],'/ajax_quesinfo', 'QuestionController@ajax_quesinfo')->name('admin.question.ajax_quesinfo');
	//获取信息
	Route::match(['get','post'],'/ajax_forminfo', 'QuestionController@ajax_forminfo')->name('admin.question.ajax_forminfo');
	//修改
	Route::match(['get','post'],'/edit_quesinfo', 'QuestionController@edit_quesinfo')->name('admin.question.edit_quesinfo');
	//导出
	Route::match(['get','post'],'/ques_export', 'QuestionController@ques_export')->name('admin.question.ques_export');
	//问卷统计
	Route::match(['get','post'],'/ques_info', 'QuestionController@ques_info')->name('admin.question.ques_info');
	//问卷作答详情统计
	Route::match(['get','post'],'/ques_textinfo', 'QuestionController@ques_textinfo')->name('admin.question.ques_textinfo');
});