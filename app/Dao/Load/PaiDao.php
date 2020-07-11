<?php

namespace App\Dao\Load;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * 随手拍模块安装模型
 *
 * @author yyj 20171213
 */
class PaiDao extends LoadDao
{

	/**
	 * 随手拍模块安装模型
	 *
	 * @author yyj 20171201
	 * @param string $type 要执行的命令
	 * @return bool|array
	 */
	public static function pai_controller($type)
	{
		$load_file_path = base_path() . '/load_file/pai/';
		$database_path = base_path() . '/database/migrations/';
		$model_path = base_path() . '/app/Models/';
		$api_path = base_path() . '/app/Http/Controllers/Api/';
		$api_and_path = base_path() . '/app/Http/Controllers/Api/Android/';
		$api_ios_path = base_path() . '/app/Http/Controllers/Api/Ios/';
		$api_web_path = base_path() . '/app/Http/Controllers/Api/Web/';
		$routes_path = base_path() . '/routes/';
		$menu_path = base_path() . '/config/load_menu/';

		$web_path = base_path() . '/app/Http/Controllers/Admin/PaiCheck/';
		$view_path = base_path() . '/resources/views/admin/paicheck/';

		//文件装载列表
		$load_file_list = [
			//数据库
			$load_file_path . '2017_12_11_000000_create_pai_comment_table.php',
			$load_file_path . '2017_12_11_000000_create_pai_like_table.php',
			$load_file_path . '2017_12_11_000000_create_pai_table.php',
			//模型
			$load_file_path . 'Pai.php',
			$load_file_path . 'PaiComment.php',
			$load_file_path . 'PaiLike.php',
			//api
			$load_file_path . 'PaiController.php',
			$load_file_path . 'AndPaiController.php',
			$load_file_path . 'IosPaiController.php',
			$load_file_path . 'WebPaiController.php',
			//web
			$load_file_path . 'PaiCheckController.php',
			//view
			$load_file_path . 'pai_comment_list.blade.php',
			$load_file_path . 'pai_list.blade.php',
			//routes
			$load_file_path . 'api_pai.php',
			$load_file_path . 'web_b_check.php',
			//menu
			$load_file_path . 'app_check_pai_menu.php',
		];

		//安装列表
		$pai_file_list = [
			//数据库
			$database_path . '2017_12_11_000000_create_pai_comment_table.php',
			$database_path . '2017_12_11_000000_create_pai_like_table.php',
			$database_path . '2017_12_11_000000_create_pai_table.php',
			//模型
			$model_path . 'Pai.php',
			$model_path . 'PaiComment.php',
			$model_path . 'PaiLike.php',
			//api
			$api_path . 'PaiController.php',
			$api_and_path . 'PaiController.php',
			$api_ios_path . 'PaiController.php',
			$api_web_path . 'PaiController.php',
			//web
			$web_path . 'PaiCheckController.php',
			//view
			$view_path . 'pai_comment_list.blade.php',
			$view_path . 'pai_list.blade.php',
			//routes
			$routes_path . 'api_pai.php',
			$routes_path . 'web_b_check.php',
			//menu
			$menu_path . 'app_check_pai_menu.php',
		];

		if ($type == 'check') {
			//文件检测
			$arr = [
				'name' => '随手拍模块',
				'key' => 'pai',
				'status' => 0,
				'des' => ''
			];
			$arr=LoadDao::check_file($arr,$load_file_list,$pai_file_list);
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
				copy($g, $pai_file_list[$k]);
			}
			//数据库相应配置初始化
			UploadedType::create([
				'type_key' => 'FT_PAI',
				'path' => 'pai',
				'desc' => '随手拍图片上传',
				'allow_type' => 'gif|jpg|jpeg|png',
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
			foreach ($pai_file_list as $k => $g) {
				if (file_exists($g)) {
					unlink($g);
				}
			}
			//目录删除
			deldir($web_path);
			deldir($view_path);
			//删除相关数据库配置
			UploadedType::where('type_key', 'FT_PAI')->delete();
			//删除相关数据库
			$arr = [
				[
					'tableName' => 'pai_comment',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'pai_like',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'pai',
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
