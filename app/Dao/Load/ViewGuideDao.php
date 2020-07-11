<?php

namespace App\Dao\Load;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * 实景导览模块安装模型
 *
 * @author yyj 20180301
 */
class ViewGuideDao extends LoadDao
{

	/**
	 * 实景导览模块安装模型
	 *
	 * @author yyj 20180301
	 * @param string $type 要执行的命令
	 * @return bool|array
	 */
	public static function view_guide_controller($type)
	{
		$load_file_path = base_path() . '/load_file/view_guide/';
		$database_path = base_path() . '/database/migrations/';
		$model_path = base_path() . '/app/Models/';
		$dao_path = base_path() . '/app/Dao/';
		$api_path = base_path() . '/app/Http/Controllers/Api/';
		$api_and_path = base_path() . '/app/Http/Controllers/Api/Android/';
		$api_ios_path = base_path() . '/app/Http/Controllers/Api/Ios/';
		$api_web_path = base_path() . '/app/Http/Controllers/Api/Web/';
		$routes_path = base_path() . '/routes/';
		$menu_path = base_path() . '/config/load_menu/';

		$web_path = base_path() . '/app/Http/Controllers/Admin/ViewGuide/';
		$view_path = base_path() . '/resources/views/admin/view_guide/';
		$api_view_path = base_path() . '/resources/views/api/view_guide/';

		//文件装载列表
		$load_file_list = [
			//api
			$load_file_path . 'ViewGuideController.php',
			$load_file_path . 'AndViewGuideController.php',
			$load_file_path . 'IosViewGuideController.php',
			$load_file_path . 'WebViewGuideController.php',
			//routes
			$load_file_path . 'api_viewguide.php',
			$load_file_path . 'web_viewguide.php',
			//数据库
			$load_file_path . '2018_07_03_000000_create_view_guide_table.php',
			//模型
			$load_file_path . 'ViewGuide.php',
			//admin
			$load_file_path . 'AdminViewGuideController.php',
			//view
			$load_file_path . 'resource_zip.blade.php',
			$load_file_path . 'view_guide_edit.blade.php',
			$load_file_path . 'view_guide_list.blade.php',
			$load_file_path . 'exhibit_content_info.blade.php',
			//menu
			$load_file_path . 'viewguide_menu.php',
		];

		//安装列表
		$install_file_list = [
			//api
			$api_path . 'ViewGuideController.php',
			$api_and_path . 'ViewGuideController.php',
			$api_ios_path . 'ViewGuideController.php',
			$api_web_path . 'ViewGuideController.php',
			//routes
			$routes_path . 'api_viewguide.php',
			$routes_path . 'web_b_viewguide.php',
			//数据库
			$database_path . '2018_07_03_000000_create_view_guide_table.php',
			//模型
			$model_path . 'ViewGuide.php',
			//admin
			$web_path . 'ViewGuideController.php',
			//view
			$view_path . 'resource_zip.blade.php',
			$view_path . 'view_guide_edit.blade.php',
			$view_path . 'view_guide_list.blade.php',
			$api_view_path . 'exhibit_content_info.blade.php',
			//menu
			$menu_path . 'viewguide_menu.php',
		];

		if ($type == 'check') {
			//文件检测
			$arr = [
				'name' => '实景导览模块',
				'key' => 'ViewGuide',
				'status' => 0,
				'des' => '需先安装SVG地图管理模块,展品相关管理模块,路线导航模块'
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
			UploadedType::create([
				'type_key' => 'FT_VIEWGUIDE',
				'path' => 'viewguide',
				'desc' => '实景导览图片多',
				'allow_type' => 'gif|jpg|jpeg|png',
				'allow_size' => '2097152',
				'allow_num' => '20'
			]);
			LoadDao::create_table_api(1, 1);
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
			deldir(base_path() . '/public/uploadfiles/viewguide_resource/');
			deldir(base_path() . '/public/uploadfiles/viewguide/');
			//删除相关数据库配置
			UploadedType::where('type_key', 'FT_VIEWGUIDE')->delete();
			//删除相关数据库
			$arr = [
				[
					'tableName' => 'view_guide',
					'primaryKey' => 'id'
				],
			];
			self::drop_table($arr);
			LoadDao::create_table_api(0, 1);
			return true;
		} else {
			return false;
		}
	}
}
