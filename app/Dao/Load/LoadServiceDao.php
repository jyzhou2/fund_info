<?php

namespace App\Dao\Load;

use App\Dao\LogDao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * 服务设施设置模块
 *
 * @author yyj 20180301
 */
class LoadServiceDao extends LoadDao
{

	/**
	 * 服务设施设置模块安装模型
	 *
	 * @author yyj 20180301
	 * @param string $type 要执行的命令
	 * @return bool|array
	 */
	public static function service_controller($type)
	{
		$load_file_path = base_path() . '/load_file/service/';
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

		$web_path = base_path() . '/app/Http/Controllers/Admin/ServicePoint/';
		$view_path = base_path() . '/resources/views/admin/service_point/';
		$api_view_path = base_path() . '/resources/views/api/service_point/';

		//文件装载列表
		$load_file_list = [
			//数据库
			$load_file_path . '2018_04_08_000000_create_service_point_table.php',
			//模型
			$load_file_path . 'ServicePoint.php',
			//api
			$load_file_path . 'ApiServicePointController.php',
			$load_file_path . 'ApiAndServicePointController.php',
			$load_file_path . 'ApiIosServicePointController.php',
			$load_file_path . 'ApiWebServicePointController.php',
			//admin
			$load_file_path . 'AdminServicePointController.php',
			//view
			$load_file_path . 'service_point_edit.blade.php',
			$load_file_path . 'service_point_list.blade.php',
			//routes
			$load_file_path . 'api_service_point.php',
			$load_file_path . 'web_service_point.php',
			//menu
			$load_file_path . 'service_point_menu.php',
		];

		//安装列表
		$install_file_list = [
			//数据库
			$database_path . '2018_04_08_000000_create_service_point_table.php',
			//模型
			$model_path . 'ServicePoint.php',
			//api
			$api_path . 'ServicePointController.php',
			$api_and_path . 'ServicePointController.php',
			$api_ios_path . 'ServicePointController.php',
			$api_web_path . 'ServicePointController.php',
			//admin
			$web_path . 'ServicePointController.php',
			//view
			$view_path . 'service_point_edit.blade.php',
			$view_path . 'service_point_list.blade.php',
			//routes
			$routes_path . 'api_service_point.php',
			$routes_path . 'web_b_service_point.php',
			//menu
			$menu_path . 'service_point_menu.php',
		];

		if ($type == 'check') {
			//文件检测
			$arr = [
				'name' => '服务设施设置模块',
				'key' => 'service_point',
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
				'type_key' => 'FT_SERVICE_POINT',
				'path' => 'service_point',
				'desc' => '服务设施点位图片',
				'allow_type' => 'gif|jpg|jpeg|png',
				'allow_size' => '2097152',
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
			UploadedType::where('type_key', 'FT_SERVICE_POINT')->delete();
			//删除相关数据库
			$arr = [
				[
					'tableName' => 'service_point',
					'primaryKey' => 'id'
				],
			];
			self::drop_table($arr);
			LoadDao::create_table_api(0,1);
			return true;
		} else {
			return false;
		}
	}
}
