<?php

namespace App\Dao\Load;

use App\Dao\LogDao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * 展品相关管理模块
 *
 * @author yyj 20180301
 */
class LoadExhibitDao extends LoadDao
{

	/**
	 * 展品相关管理模块安装模型
	 *
	 * @author yyj 20180301
	 * @param string $type 要执行的命令
	 * @return bool|array
	 */
	public static function exhibit_controller($type)
	{
		$load_file_path = base_path() . '/load_file/exhibit/';
		$database_path = base_path() . '/database/migrations/';
		$model_path = base_path() . '/app/Models/';
		$dao_path = base_path() . '/app/Dao/';
		$api_path = base_path() . '/app/Http/Controllers/Api/';
		$api_and_path = base_path() . '/app/Http/Controllers/Api/Android/';
		$api_ios_path = base_path() . '/app/Http/Controllers/Api/Ios/';
		$api_web_path = base_path() . '/app/Http/Controllers/Api/Web/';
		$routes_path = base_path() . '/routes/';
		$menu_path = base_path() . '/config/load_menu/';
		$config_path = base_path() . '/config/';

		$web_path = base_path() . '/app/Http/Controllers/Admin/Data/';
		$view_path = base_path() . '/resources/views/admin/data/';
		$api_view_path = base_path() . '/resources/views/api/exhibit/';

		//文件装载列表
		$load_file_list = [
			//数据库
			$load_file_path . '2018_03_03_000000_create_exhibition_language_table.php',
			$load_file_path . '2018_03_03_000000_create_exhibition_table.php',
			$load_file_path . '2018_03_03_000000_create_exhibit_language_table.php',
			$load_file_path . '2018_03_03_000000_create_exhibit_table.php',
			$load_file_path . '2018_03_03_000000_create_ex_user_visit_table.php',
			$load_file_path . '2018_03_03_000000_create_exhibit_comment_table.php',
			$load_file_path . '2018_03_03_000000_create_exhibit_like_table.php',
			$load_file_path . '2018_03_03_000000_create_exhibit_comment_likelist_table.php',
			$load_file_path . '2018_03_03_000000_create_autonum_list_table.php',
			$load_file_path . '2018_03_06_000000_create_version_list_table.php',
			//模型
			$load_file_path . 'Exhibition.php',
			$load_file_path . 'ExhibitionLanguage.php',
			$load_file_path . 'Autonum.php',
			$load_file_path . 'Exhibit.php',
			$load_file_path . 'ExhibitComment.php',
			$load_file_path . 'ExhibitCommentLikelist.php',
			$load_file_path . 'ExhibitLanguage.php',
			$load_file_path . 'ExhibitLike.php',
			$load_file_path . 'ExUserVisit.php',
			$load_file_path . 'VersionList.php',
			//api
			$load_file_path . 'ApiExhibitController.php',
			$load_file_path . 'ApiMapExhibitController.php',
			$load_file_path . 'ApiMyExhibitController.php',
			$load_file_path . 'ApiAndExhibitController.php',
			$load_file_path . 'ApiAndMapExhibitController.php',
			$load_file_path . 'ApiAndMyExhibitController.php',
			$load_file_path . 'ApiIosExhibitController.php',
			$load_file_path . 'ApiIosMapExhibitController.php',
			$load_file_path . 'ApiIosMyExhibitController.php',
			$load_file_path . 'ApiWebExhibitController.php',
			$load_file_path . 'ApiWebMapExhibitController.php',
			$load_file_path . 'ApiWebMyExhibitController.php',
			//Dao
			$load_file_path . 'ExhibitDao.php',
			$load_file_path . 'ResourceDao.php',
			//admin
			$load_file_path . 'AdminAutonumController.php',
			$load_file_path . 'AdminExhibitController.php',
			$load_file_path . 'AdminExhibitionController.php',
			//view
			$load_file_path . 'autonum_list.blade.php',
			$load_file_path . 'autonum_edit.blade.php',
			$load_file_path . 'exhibit_list.blade.php',
			$load_file_path . 'exhibit_edit.blade.php',
			$load_file_path . 'exhibition_list.blade.php',
			$load_file_path . 'exhibition_edit.blade.php',
			$load_file_path . 'resource_zip.blade.php',
			$load_file_path . 'exhibit_comment_list.blade.php',
			$load_file_path . 'exhibition_comment_list.blade.php',
			$load_file_path .'exhibit_set_order.blade.php',
			$load_file_path .'exhibition_set_order.blade.php',
			$load_file_path .'api_exhibit_content_info.blade.php',
			$load_file_path .'api_exhibit_knowledge_info.blade.php',
			$load_file_path .'api_exhibit_share_info.blade.php',
			//routes
			$load_file_path . 'api_exhibit.php',
			$load_file_path . 'web_exhibit.php',
			//menu
			$load_file_path . 'exhibit_menu.php',
			//config
			$load_file_path . 'exhibit_config.php'
		];

		//安装列表
		$install_file_list = [
			//数据库
			$database_path . '2018_03_03_000000_create_exhibition_language_table.php',
			$database_path . '2018_03_03_000000_create_exhibition_table.php',
			$database_path . '2018_03_03_000000_create_exhibit_language_table.php',
			$database_path . '2018_03_03_000000_create_exhibit_table.php',
			$database_path . '2018_03_03_000000_create_ex_user_visit_table.php',
			$database_path . '2018_03_03_000000_create_exhibit_comment_table.php',
			$database_path . '2018_03_03_000000_create_exhibit_like_table.php',
			$database_path . '2018_03_03_000000_create_exhibit_comment_likelist_table.php',
			$database_path . '2018_03_03_000000_create_autonum_list_table.php',
			$database_path . '2018_03_06_000000_create_version_list_table.php',
			//模型
			$model_path . 'Exhibition.php',
			$model_path . 'ExhibitionLanguage.php',
			$model_path . 'Autonum.php',
			$model_path . 'Exhibit.php',
			$model_path . 'ExhibitComment.php',
			$model_path . 'ExhibitCommentLikelist.php',
			$model_path . 'ExhibitLanguage.php',
			$model_path . 'ExhibitLike.php',
			$model_path . 'ExUserVisit.php',
			$model_path . 'VersionList.php',
			//api
			$api_path . 'ExhibitController.php',
			$api_path . 'MapExhibitController.php',
			$api_path . 'MyExhibitController.php',
			$api_and_path . 'ExhibitController.php',
			$api_and_path . 'MapExhibitController.php',
			$api_and_path . 'MyExhibitController.php',
			$api_ios_path . 'ExhibitController.php',
			$api_ios_path . 'MapExhibitController.php',
			$api_ios_path . 'MyExhibitController.php',
			$api_web_path . 'ExhibitController.php',
			$api_web_path . 'MapExhibitController.php',
			$api_web_path . 'MyExhibitController.php',
			//Dao
			$dao_path . 'ExhibitDao.php',
			$dao_path . 'ResourceDao.php',
			//admin
			$web_path . 'AutonumController.php',
			$web_path . 'ExhibitController.php',
			$web_path . 'ExhibitionController.php',
			//view
			$view_path . 'autonum_list.blade.php',
			$view_path . 'autonum_edit.blade.php',
			$view_path . 'exhibit_list.blade.php',
			$view_path . 'exhibit_edit.blade.php',
			$view_path . 'exhibition_list.blade.php',
			$view_path . 'exhibition_edit.blade.php',
			$view_path . 'resource_zip.blade.php',
			$view_path . 'exhibit_comment_list.blade.php',
			$view_path . 'exhibition_comment_list.blade.php',
			$view_path .'exhibit_set_order.blade.php',
			$view_path .'exhibition_set_order.blade.php',
			$api_view_path .'exhibit_content_info.blade.php',
			$api_view_path .'exhibit_knowledge_info.blade.php',
			$api_view_path .'exhibit_share_info.blade.php',
			//routes
			$routes_path . 'api_exhibit.php',
			$routes_path . 'web_b_exhibit.php',
			//menu
			$menu_path . 'exhibit_menu.php',
			//config
			$config_path . 'exhibit_config.php'
		];

		if ($type == 'check') {
			//文件检测
			$arr = [
				'name' => '展品相关管理模块',
				'key' => 'exhibit',
				'status' => 0,
				'des' => '需先安装SVG地图管理模块'
			];
			$arr = LoadDao::check_file($arr, $load_file_list, $install_file_list);
			return $arr;
		} elseif ($type == 'install') {
			//目录生成
			if (!file_exists($web_path)) {
				mkdir($web_path, 0777, true);
			}
			if (!file_exists($view_path)) {
				mkdir($view_path, 0777, true);
			}
			if (!file_exists($api_view_path)) {
				mkdir($api_view_path, 0777, true);
			}
			//文件复制
			foreach ($load_file_list as $k => $g) {
				copy($g, $install_file_list[$k]);
			}
			//数据库相应配置初始化
			UploadedType::create([
				'type_key' => 'FT_EXHIBIT_ONE',
				'path' => 'exhibt',
				'desc' => '展品图片单',
				'allow_type' => 'gif|jpg|jpeg|png',
				'allow_size' => '2097152',
				'allow_num' => '1'
			]);
			UploadedType::create([
				'type_key' => 'FT_EXHIBIT_MORE',
				'path' => 'exhibt',
				'desc' => '展品图片多',
				'allow_type' => 'gif|jpg|jpeg|png',
				'allow_size' => '2097152',
				'allow_num' => '10'
			]);
			UploadedType::create([
				'type_key' => 'FT_EXHIBIT_MP3',
				'path' => 'exhibt_mp3',
				'desc' => '展品mp3',
				'allow_type' => 'mp3',
				'allow_size' => '20971520',
				'allow_num' => '1'
			]);
			//相关数据库接口文档生成
			/* if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
				 //echo '这个服务器操作系统为Windows!';
			 }else{
				 //echo '这个服务器操作系统不是Windows系统!';
			 }*/
			LoadDao::create_table_api(1,1);
			return true;
		} elseif ($type == 'uninstall') {
			//文件删除
			foreach ($install_file_list as $k => $g) {
				if (file_exists($g)) {
					unlink($g);
				}
			}
			//目录删除
			deldir($web_path);
			deldir($view_path);
			deldir($api_view_path);
			//删除相关数据库配置
			UploadedType::where('type_key', 'FT_EXHIBIT_ONE')->delete();
			UploadedType::where('type_key', 'FT_EXHIBIT_MORE')->delete();
			UploadedType::where('type_key', 'FT_EXHIBIT_MP3')->delete();
			//删除相关数据库
			$arr = [
				[
					'tableName' => 'autonum_list',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'ex_user_visit',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'exhibit_comment_likelist',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'exhibit_comment',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'exhibit_language',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'exhibit_like',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'exhibit',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'exhibition_language',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'exhibition',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'version_list',
					'primaryKey' => 'id'
				]
			];
			self::drop_table($arr);
			LoadDao::create_table_api(0,1);
			return true;
		} else {
			return false;
		}
	}
}
