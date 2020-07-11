<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 16:56
 */
return [
	[
		'text' => '展厅管理',
		'url' => '/' . env('ADMIN_ENTRANCE', 'admin') . '/data/exhibition',
		'priv' => 'admin-data-exhibition',
		'order_num' => 10,
	],
	[
		'text' => '展品管理',
		'url' => '/' . env('ADMIN_ENTRANCE', 'admin') . '/data/exhibit',
		'priv' => 'admin-data-exhibit',
		'order_num' => 10,
	],
	[
		'text' => '蓝牙号关联设置',
		'url' => '/' . env('ADMIN_ENTRANCE', 'admin') . '/data/autonum',
		'priv' => 'admin-data-autonum',
		'order_num' => 10,
	],
];