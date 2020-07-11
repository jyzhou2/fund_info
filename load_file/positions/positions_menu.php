<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/7/21
 * Time: 9:19
 */
return [
	[
		'text' => '观众定位',
		'url' => '/' . env('ADMIN_ENTRANCE', 'admin') .'/positions/positions_list/',
		'priv' => 'admin-positions-positions:positions_list',
		'order_num' => 10,
	],
	[
		'text' => '轨迹追溯',
		'url' => '/' . env('ADMIN_ENTRANCE', 'admin') .'/positions/rent_trajectory_list/',
		'priv' => 'admin-positions-trajectory',
		'order_num' => 10,
	],
];