<?php

namespace App\Dao\Load;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * SVG地图模块安装模型
 *
 * @author yyj 20180301
 */
class SvgMapDao extends LoadDao
{

	/**
	 * SVG地图模块安装模型
	 *
	 * @author yyj 20180301
	 * @param string $type 要执行的命令
	 * @return bool|array
	 */
	public static function svg_map_controller($type)
	{
		$load_file_path = base_path() . '/load_file/svgmap/';
		$database_path = base_path() . '/database/migrations/';
		$model_path = base_path() . '/app/Models/';
		$api_path = base_path() . '/app/Http/Controllers/Api/';
		$api_and_path = base_path() . '/app/Http/Controllers/Api/Android/';
		$api_ios_path = base_path() . '/app/Http/Controllers/Api/Ios/';
		$api_web_path = base_path() . '/app/Http/Controllers/Api/Web/';
		$routes_path = base_path() . '/routes/';
		$menu_path = base_path() . '/config/load_menu/';

		$web_path = base_path() . '/app/Http/Controllers/Admin/SvgMapAdmin/';
		$view_path = base_path() . '/resources/views/admin/svgmap/';

		//文件装载列表
		$load_file_list = [
			//数据库
			$load_file_path . '2018_03_01_000000_create_svgmap_table.php',
			//模型
			$load_file_path . 'SvgMapTable.php',
			//api
			$load_file_path . 'SvgMapController.php',
			$load_file_path . 'AndSvgMapController.php',
			$load_file_path . 'IosSvgMapController.php',
			$load_file_path . 'WebSvgMapController.php',
			//admin
			$load_file_path . 'SvgMapAdminController.php',
			//view
			$load_file_path . 'svgmap_edit.blade.php',
			$load_file_path . 'svgmap_list.blade.php',
			$load_file_path . 'svgmap_view.blade.php',
			//routes
			$load_file_path . 'api_svgmap.php',
			$load_file_path . 'web_svgmap.php',
			//menu
			$load_file_path . 'svgmap_menu.php',
		];

		//安装列表
		$install_file_list = [
			//数据库
			$database_path . '2018_03_01_000000_create_svgmap_table.php',
			//模型
			$model_path . 'SvgMapTable.php',
			//api
			$api_path . 'SvgMapController.php',
			$api_and_path . 'SvgMapController.php',
			$api_ios_path . 'SvgMapController.php',
			$api_web_path . 'SvgMapController.php',
			//admin
			$web_path . 'SvgMapAdminController.php',
			//view
			$view_path . 'svgmap_edit.blade.php',
			$view_path . 'svgmap_list.blade.php',
			$view_path . 'svgmap_view.blade.php',
			//routes
			$routes_path . 'api_svgmap.php',
			$routes_path . 'web_b_svgmap.php',
			//menu
			$menu_path . 'svgmap_menu.php',
		];

		if ($type == 'check') {
			//文件检测
			$arr = [
				'name' => 'SVG地图管理模块',
				'key' => 'svg_map',
				'status' => 0,
				'des' => ''
			];
			$arr=LoadDao::check_file($arr,$load_file_list,$install_file_list);
			return $arr;
		} elseif ($type == 'install') {
			//目录生成
			if (!file_exists($web_path)) {
				mkdir($web_path, 0777, true);
			}
			if (!file_exists($view_path)) {
				mkdir($view_path, 0777, true);
			}
			//文件复制
			foreach ($load_file_list as $k => $g) {
				copy($g, $install_file_list[$k]);
			}
			//数据库相应配置初始化
			UploadedType::create([
				'type_key' => 'FT_SVGMAP',
				'path' => 'svgmap',
				'desc' => 'svg地图上传',
				'allow_type' => 'svg',
				'allow_size' => '5242880',
				'allow_num' => '1'
			]);
			UploadedType::create([
				'type_key' => 'FT_PNGMAP',
				'path' => 'pngmap',
				'desc' => 'png地图上传',
				'allow_type' => 'png',
				'allow_size' => '5242880',
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
			//删除相关数据库配置
			UploadedType::where('type_key', 'FT_SVGMAP')->delete();
			//删除相关数据库
			$arr = [
				[
					'tableName' => 'svgmap',
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
