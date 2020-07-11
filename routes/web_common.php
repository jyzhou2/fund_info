<?php

/**
 * 通用路由
 */
// 验证码显示 - 通用
Route::get('cpt/show', 'CptController@show');
// 验证码验证 - 通用
Route::get('cpt/check', 'CptController@check');
// UEditor
Route::get('/ueditor/config', 'UeditorController@config');
Route::post('/ueditor/uploadimage', 'UeditorController@uploadimage');
Route::post('/ueditor/uploadfile', 'UeditorController@uploadfile');
Route::post('/ueditor/uploadvideo', 'UeditorController@uploadvideo');

// 获取访问客户端ip
Route::get('ip', function () {
	return response(client_real_ip());
});

// 格式化php代码，去掉注释，多用于项目申报
Route::match([
	'get',
	'post'
], 'phpcode', function () {
	$show = '';
	if (request('code')) {
		$show = m_phpcode_format(request('code'));
	}
	return view('phpcode', [
		'show' => $show,
		'code' => request('code')
	]);
});

// Test
Route::get('test', function () {
});
