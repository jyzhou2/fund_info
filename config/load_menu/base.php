<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/12/11
 * Time: 16:46
 */
$arr['add']=[];
$base_path=base_path().'/config/load_menu/';

//审核管理模块
$check_nodes=[];
if(count($check_nodes)>0||file_exists($base_path.'app_check_pai_menu.php')||config('exhibit_config.is_show_exhibition_comment')||config('exhibit_config.is_show_exhibit_comment')){
	//添加随手拍
	if(file_exists($base_path.'app_check_pai_menu.php')){
		$check_nodes[]=include $base_path.'app_check_pai_menu.php';
	}
	//添加展厅评论审核
	if(config('exhibit_config.is_show_exhibition_comment')){
		$check_nodes[]=[
			'text' => '展厅评论审核',
			'url' => '/' . env('ADMIN_ENTRANCE', 'admin') .'/data/exhibition/exhibition_comment_list',
			'priv' => 'admin-data-exhibition:exhibition_comment_list',
			'order_num' => 10,
		];
	}
	//添加展品评论审核
	if(config('exhibit_config.is_show_exhibit_comment')){
		$check_nodes[]=[
			'text' => '展品评论审核',
			'url' => '/' . env('ADMIN_ENTRANCE', 'admin') .'/data/exhibit/exhibit_comment_list',
			'priv' => 'admin-data-exhibit:exhibit_comment_list',
			'order_num' => 10,
		];
	}
	$arr['add'][]=[
		'text' => '审核管理',
		'icon' => 'fa fa-check-square',
		'priv' => 'check',
		'nodes' => $check_nodes,
		'order_num'=>10,
	];
}
//数据管理
$data_nodes=[];
if(count($data_nodes)>0||file_exists($base_path.'exhibit_menu.php')||file_exists($base_path.'service_point_menu.php')){
	//添加展品相关数据管理
	if(file_exists($base_path.'exhibit_menu.php')){
		$exhibit_arr=include $base_path.'exhibit_menu.php';
		$data_nodes=array_merge($exhibit_arr,$data_nodes);
	}
	//添加资源打包更新
	if(file_exists(base_path().'/config/exhibit_config.php')&&config('exhibit_config.is_version_zip')){
		$data_nodes[]=[
			'text' => '资源打包更新',
			'url' => '/' . env('ADMIN_ENTRANCE', 'admin') .'/data/exhibit/resource_zip',
			'priv' => 'admin-data-exhibit:resource_zip',
			'order_num' => 10,
		];
	}
	//添加服务点位管理
	if(file_exists($base_path.'service_point_menu.php')){
		$data_nodes[]=include $base_path.'service_point_menu.php';
	}
	//添加实景导览管理
	if(file_exists($base_path.'viewguide_menu.php')){
		$data_nodes[]=include $base_path.'viewguide_menu.php';
	}

	$arr['add'][]=[
		'text' => '数据管理',
		'icon' => 'fa fa-table',
		'priv' => 'data',
		'nodes' => $data_nodes,
		'order_num'=>10,
	];
}
//服务管理
$service_nodes=[];
if(count($service_nodes)>0||file_exists($base_path.'question_menu.php')||file_exists($base_path.'feedback_menu.php')){
	//添加问卷调查
	if(file_exists($base_path.'question_menu.php')){
		$service_nodes[]=include $base_path.'question_menu.php';
	}
	//添加意见反馈
	if(file_exists($base_path.'feedback_menu.php')){
		$service_nodes[]=include $base_path.'feedback_menu.php';
	}
	$arr['add'][]=[
		'text' => '服务管理',
		'icon' => 'fa fa-refresh',
		'priv' => 'service',
		'nodes' => $service_nodes,
		'order_num'=>10,
	];
}

//定位管理
$positions_nodes=[];
if(count($positions_nodes)>0||file_exists($base_path.'positions_menu.php')){
	//添加意见反馈
	if(file_exists($base_path.'positions_menu.php')){
		$positions_arr=include $base_path.'positions_menu.php';
		$positions_nodes=array_merge($positions_arr,$positions_nodes);
	}
	$arr['add'][]=[
		'text' => '观众定位',
		'icon' => 'fa fa-location-arrow',
		'priv' => 'positions',
		'nodes' => $positions_nodes,
		'order_num'=>10,
	];
}



//SVG地图管理
if(file_exists($base_path.'svgmap_menu.php')){
	$arr['update']['setting'][]=include $base_path.'svgmap_menu.php';
}

//路网管理
if(file_exists($base_path.'navigation_menu.php')){
	$arr['update']['setting'][]=include $base_path.'navigation_menu.php';
}

//模块装载管理
if(file_exists($base_path.'load_menu.php')){
	$arr['update']['setting'][]=include $base_path.'load_menu.php';
}
return $arr;