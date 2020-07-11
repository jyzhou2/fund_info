<?php
Route::group([
	'prefix' => 'feedback',
	'namespace' => 'Feedback'
], function () {
	Route::get('/index', 'FeedbackController@index')->name('admin.feedback.index');
	Route::get('/reply', 'FeedbackController@reply')->name('admin.feedback.reply');
	Route::post('/reply_save', 'FeedbackController@reply_save')->name('admin.feedback.reply_save');
	Route::get('/delete/{id}', 'FeedbackController@delete')->name('admin.feedback.delete');
});