<?php

namespace App\Dao\Load;

use App\Models\SvgMapTable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * 路线导航模块安装模型
 *
 * @author yyj 20180301
 */
class NavigationRoadDao extends LoadDao
{

	/**
	 * 路线导航模块安装模型
	 *
	 * @author yyj 20180301
	 * @param string $type 要执行的命令
	 * @return bool|array
	 */
	public static function navigation_road_controller($type)
	{
		$load_file_path = base_path() . '/load_file/navigation_road/';
		$database_path = base_path() . '/database/migrations/';
		$model_path = base_path() . '/app/Models/';
		$dao_path = base_path() . '/app/Dao/';
		$api_path = base_path() . '/app/Http/Controllers/Api/';
		$api_and_path = base_path() . '/app/Http/Controllers/Api/Android/';
		$api_ios_path = base_path() . '/app/Http/Controllers/Api/Ios/';
		$api_web_path = base_path() . '/app/Http/Controllers/Api/Web/';
		$routes_path = base_path() . '/routes/';
		$menu_path = base_path() . '/config/load_menu/';

		$web_path = base_path() . '/app/Http/Controllers/Admin/NavigationRoad/';
		$view_path = base_path() . '/resources/views/admin/navigation_road/';
		$api_view_path = base_path() . '/resources/views/api/navigation_road/';

		//文件装载列表
		$load_file_list = [
			//数据库
			$load_file_path . '2018_06_30_000000_create_navigation_point_table.php',
			$load_file_path . '2018_06_30_000000_create_navigation_road_table.php',
			//模型
			$load_file_path . 'NavigationPoint.php',
			$load_file_path . 'NavigationRoad.php',
			//api
			$load_file_path . 'NavigationRoadController.php',
			$load_file_path . 'AndNavigationRoadController.php',
			$load_file_path . 'IosNavigationRoadController.php',
			$load_file_path . 'WebNavigationRoadController.php',
			//admin
			$load_file_path . 'NavigationRoadAdminController.php',
			//view
			$load_file_path . 'navigation_edit.blade.php',
			$load_file_path . 'dh_test.blade.php',
			//routes
			$load_file_path . 'api_navigation.php',
			$load_file_path . 'web_navigation.php',
			//menu
			$load_file_path . 'navigation_menu.php',
			//Dao
			$load_file_path . 'NavigationRoadDao.php',
		];

		//安装列表
		$install_file_list = [
			//数据库
			$database_path . '2018_06_30_000000_create_navigation_point_table.php',
			$database_path . '2018_06_30_000000_create_navigation_road_table.php',
			//模型
			$model_path . 'NavigationPoint.php',
			$model_path . 'NavigationRoad.php',
			//api
			$api_path . 'NavigationRoadController.php',
			$api_and_path . 'NavigationRoadController.php',
			$api_ios_path . 'NavigationRoadController.php',
			$api_web_path . 'NavigationRoadController.php',
			//admin
			$web_path . 'NavigationRoadController.php',
			//view
			$view_path . 'navigation_edit.blade.php',
			$api_view_path . 'dh_test.blade.php',
			//routes
			$routes_path . 'api_navigation.php',
			$routes_path . 'web_b_navigation.php',
			//menu
			$menu_path . 'navigation_menu.php',
			//Dao
			$dao_path . 'NavigationRoadDao.php',
		];

		if ($type == 'check') {
			//文件检测
			$arr = [
				'name' => '路线导航模块',
				'key' => 'navigation_road',
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
			SvgMapTable::where('id', '<>', 0)->update([
				'route_point_list' => '',
				'route_line_list' => ''
			]);
			//删除相关数据库
			$arr = [
				[
					'tableName' => 'navigation_point',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'navigation_road',
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
