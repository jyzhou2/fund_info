<?php
//问卷调查页面
Route::match([
	'get',
	'post'
], 'question/quesinfo', 'QuestionController@quesinfo');
//问卷调查列表
Route::match([
	'get',
	'post'
], 'question/question_list', 'QuestionController@question_list');
//问卷题库
Route::get('question/get_question', 'QuestionController@get_question');

Route::match([
	'get',
	'post'
], 'question/postquesinfo_new', 'QuestionController@postquesinfo_new');