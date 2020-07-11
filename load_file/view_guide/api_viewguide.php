<?php
//实景导览模块

//获取周围展品
Route::get('viewguide/near_exhibit', 'ViewGuideController@near_exhibit');

//获取展品详情
Route::get('viewguide/exhibit_info', 'ViewGuideController@exhibit_info');

//最短路线获取
Route::get('viewguide/shortest_route', 'ViewGuideController@shortest_route');

//获取数据库资源
Route::get('viewguide/get_data', 'ViewGuideController@get_data');

//展品详情页
Route::get('viewguide/exhibit_content_info/{language}/{exhibit_id}', 'ViewGuideController@exhibit_content_info');