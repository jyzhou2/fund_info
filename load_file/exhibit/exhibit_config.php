<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2018/3/5
 * Time: 15:16
 */
return [
	//是否开启资源打包更新
	'is_version_zip' => true,
	//不打包的文件
	'zip_exclude' => [
		str_replace("\\", '/', base_path() . '/public/resource_zip/exhibit_knowledge.html'),
		str_replace("\\", '/', base_path() . '/public/resource_zip/exhibit_content.html'),
		str_replace("\\", '/', base_path() . '/public/resource_zip/exhibition.html'),
	],
	//展厅配置
	'exhibition' => [
		//是否配置轮播
		'is_lb' => true,
		//轮播数量限制
		'lb_num' => 5,
		//是否配置显示
		'is_show' => true,
		//是否配置编辑临展
		'is_linzhan' => true,
		//是否配置编辑周围展厅
		'is_near_exhibition' => true,
		//图片资源上传配置，后台列表默认显示imgs数组中第一个图片信息
		'imgs' => [
			[
				//图片字段存储键名,表单提交时的名称，不能与表单现有name重复
				'key' => 'list_img',
				//数据库存储类型
				'upload_key' => 'FT_EXHIBIT_ONE',
				//页面提示名称
				'name' => '列表图片',
				//是否必传
				'required' => true,
				//是否上传多图,该类型必须和数据库存储类型上传数量保持一致
				'is_more' => false,
				//该资源是否打包
				'is_zip' => true,
				//资源打包时生成的图片名称
				'zip_name' => 'list'
			],
		]
	],
	//展品配置
	'exhibit' => [
		//是否配置轮播
		'is_lb' => true,
		//轮播数量限制
		'lb_num' => 5,
		'imgs' => [
			[
				//图片字段存储键名,表单提交时的名称，不能与表单现有name重复
				'key' => 'exhibit_icon1',
				//数据库存储类型
				'upload_key' => 'FT_EXHIBIT_ONE',
				//页面提示名称
				'name' => '地图页icon(亮)',
				//是否必传
				'required' => true,
				//是否上传多图,该类型必须和数据库存储类型上传数量保持一致
				'is_more' => false,
				//该资源是否打包
				'is_zip' => true,
				//资源打包时生成的图片名称
				'zip_name' => 'icon1'
			],
			[
				//图片字段存储键名,表单提交时的名称，不能与表单现有name重复
				'key' => 'exhibit_icon2',
				//数据库存储类型
				'upload_key' => 'FT_EXHIBIT_ONE',
				//页面提示名称
				'name' => '地图页icon(暗)',
				//是否必传
				'required' => true,
				//是否上传多图,该类型必须和数据库存储类型上传数量保持一致
				'is_more' => false,
				//该资源是否打包
				'is_zip' => true,
				//资源打包时生成的图片名称
				'zip_name' => 'icon2'
			],
			[
				//图片字段存储键名,表单提交时的名称，不能与表单现有name重复
				'key' => 'exhibit_list',
				//数据库存储类型
				'upload_key' => 'FT_EXHIBIT_ONE',
				//页面提示名称
				'name' => '列表图片',
				//是否必传
				'required' => true,
				//是否上传多图,该类型必须和数据库存储类型上传数量保持一致
				'is_more' => false,
				//该资源是否打包
				'is_zip' => true,
				//资源打包时生成的图片名称
				'zip_name' => 'list'
			],
			[
				//图片字段存储键名,表单提交时的名称，不能与表单现有name重复
				'key' => 'exhibit_imgs',
				//数据库存储类型
				'upload_key' => 'FT_EXHIBIT_MORE',
				//页面提示名称
				'name' => '展品详情图片',
				//是否必传
				'required' => true,
				//是否上传多图,该类型必须和数据库存储类型上传数量保持一致
				'is_more' => true,
				//该资源是否打包
				'is_zip' => true,
				//资源打包时生成的图片名称
				'zip_name' => 'imgs'
			],
		],
		//展品html字段，数据库初始默认含有content，knowledge两个字段,如需要编辑其他字段需要手动修改数据库文件2018_03_03_000000_create_exhibit_language_table.php，所添加的字段必须与content_arr中的key保持一致，如果资源需要导览机打包下载，需要在/public/resource_zip/下添加模板文件及css文件
		'content_arr' => [
			[
				'name' => '展品简介',
				'key' => 'content',
			],
			/*[
				'name' => '科普知识',
				'key' => 'knowledge',
			],*/
		],
	],
	//是否手动标记蓝牙定位点坐标
	'is_set_autonum_x_y' => false,
	//是否显示展厅评论审核
	'is_show_exhibition_comment' => true,
	//是否显示展品评论审核
	'is_show_exhibit_comment' => true,
	//蓝牙配置是否多对多
	'exhibit_more_autonum'=>false,
];