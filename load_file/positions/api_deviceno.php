<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/11/10
 * Time: 11:16
 */
//机器号请求接口
Route::get('request_deviceno', 'DevicenoController@request_deviceno');
//心跳上传接口
Route::post('heartbeat', 'DevicenoController@heartbeat');
//定位上传接口
Route::post('positions', 'DevicenoController@positions');
