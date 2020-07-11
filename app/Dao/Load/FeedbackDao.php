<?php

namespace App\Dao\Load;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * 意见反馈模块安装模型
 *
 * @author yyj 20180301
 */
class FeedbackDao extends LoadDao
{

	/**
	 * 意见反馈模块安装模型
	 *
	 * @author yyj 20180717
	 * @param string $type 要执行的命令
	 * @return bool|array
	 */
	public static function feedback_controller($type)
	{
		$load_file_path = base_path() . '/load_file/feedback/';
		$database_path = base_path() . '/database/migrations/';
		$model_path = base_path() . '/app/Models/';
		$dao_path = base_path() . '/app/Dao/';
		$api_path = base_path() . '/app/Http/Controllers/Api/';
		$api_and_path = base_path() . '/app/Http/Controllers/Api/Android/';
		$api_ios_path = base_path() . '/app/Http/Controllers/Api/Ios/';
		$api_web_path = base_path() . '/app/Http/Controllers/Api/Web/';
		$routes_path = base_path() . '/routes/';
		$menu_path = base_path() . '/config/load_menu/';

		$web_path = base_path() . '/app/Http/Controllers/Admin/Feedback/';
		$view_path = base_path() . '/resources/views/admin/feedback/';
		$api_view_path = base_path() . '/resources/views/api/feedback/';

		//文件装载列表
		$load_file_list = [
			//api
			$load_file_path . 'FeedbackController.php',
			$load_file_path . 'AndFeedbackController.php',
			$load_file_path . 'IosFeedbackController.php',
			$load_file_path . 'WebFeedbackController.php',
			//routes
			$load_file_path . 'api_feedback.php',
			$load_file_path . 'web_feedback.php',
			//数据库
			$load_file_path . '2018_07_17_000000_create_feedback_table.php',
			//模型
			$load_file_path . 'Feedback.php',
			//admin
			$load_file_path . 'AdminFeedbackController.php',
			//view
			$load_file_path . 'index.blade.php',
			$load_file_path . 'reply.blade.php',
			//menu
			$load_file_path . 'feedback_menu.php',
		];

		//安装列表
		$install_file_list = [
			//api
			$api_path . 'FeedbackController.php',
			$api_and_path . 'FeedbackController.php',
			$api_ios_path . 'FeedbackController.php',
			$api_web_path . 'FeedbackController.php',
			//routes
			$routes_path . 'api_feedback.php',
			$routes_path . 'web_b_feedback.php',
			//数据库
			$database_path . '2018_07_17_000000_create_feedback_table.php',
			//模型
			$model_path . 'Feedback.php',
			//admin
			$web_path . 'FeedbackController.php',
			//view
			$view_path . 'index.blade.php',
			$view_path . 'reply.blade.php',
			//menu
			$menu_path . 'feedback_menu.php',
		];

		if ($type == 'check') {
			//文件检测
			$arr = [
				'name' => '意见反馈',
				'key' => 'Feedback',
				'status' => 0,
				'des' => ''
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
				'type_key' => 'FT_FEEDBACK',
				'path' => 'feedback',
				'desc' => '意见反馈图片',
				'allow_type' => 'gif|jpg|jpeg|png',
				'allow_size' => '5242880',
				'allow_num' => '1'
			]);
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
			UploadedType::where('type_key', 'FT_FEEDBACK')->delete();
			//删除相关数据库
			$arr = [
				[
					'tableName' => 'feedback',
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
