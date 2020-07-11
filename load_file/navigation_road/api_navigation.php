<?php
//导航测试
Route::match([
	'get',
	'post'
], 'navigation/dh_test', 'NavigationRoadController@dh_test')->name('api.navigation.dh_test');