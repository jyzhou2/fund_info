<?php

namespace App\Dao\Load;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * 问卷调查模块安装模型
 *
 * @author yyj 20180301
 */
class QuestionDao extends LoadDao
{

	/**
	 * 问卷调查模块安装模型
	 *
	 * @author yyj 20180301
	 * @param string $type 要执行的命令
	 * @return bool|array
	 */
	public static function question_controller($type)
	{
		$load_file_path = base_path() . '/load_file/question/';
		$database_path = base_path() . '/database/migrations/';
		$model_path = base_path() . '/app/Models/';
		$dao_path = base_path() . '/app/Dao/';
		$api_path = base_path() . '/app/Http/Controllers/Api/';
		$api_and_path = base_path() . '/app/Http/Controllers/Api/Android/';
		$api_ios_path = base_path() . '/app/Http/Controllers/Api/Ios/';
		$api_web_path = base_path() . '/app/Http/Controllers/Api/Web/';
		$routes_path = base_path() . '/routes/';
		$menu_path = base_path() . '/config/load_menu/';

		$web_path = base_path() . '/app/Http/Controllers/Admin/Question/';
		$view_path = base_path() . '/resources/views/admin/question/';
		$api_view_path = base_path() . '/resources/views/api/question/';

		//文件装载列表
		$load_file_list = [
			//api
			$load_file_path . 'QuestionController.php',
			$load_file_path . 'AndQuestionController.php',
			$load_file_path . 'IosQuestionController.php',
			$load_file_path . 'WebQuestionController.php',
			//routes
			$load_file_path . 'api_question.php',
			$load_file_path . 'web_question.php',
			//数据库
			$load_file_path . '2018_07_06_000000_create_ques_textinfo_table.php',
			$load_file_path . '2018_07_06_000000_create_quesinfo_list_table.php',
			$load_file_path . '2018_07_06_000000_create_quesinfo_option_table.php',
			$load_file_path . '2018_07_06_000000_create_queslist_table.php',
			//模型
			$load_file_path . 'QuesinfoList.php',
			$load_file_path . 'QuesinfoOption.php',
			$load_file_path . 'Queslist.php',
			$load_file_path . 'QuesTextinfo.php',
			//admin
			$load_file_path . 'AdminQuestionController.php',
			//view
			$load_file_path . 'edit_ques.blade.php',
			$load_file_path . 'ques_list.blade.php',
			$load_file_path . 'ques_textinfo.blade.php',
			$load_file_path . 'quesinfo_list.blade.php',
			$load_file_path . 'ques_info.blade.php',
			$load_file_path . 'ques_html_end.blade.php',
			$load_file_path . 'ques_html_start.blade.php',
			//menu
			$load_file_path . 'question_menu.php',
		];

		//安装列表
		$install_file_list = [
			//api
			$api_path . 'QuestionController.php',
			$api_and_path . 'QuestionController.php',
			$api_ios_path . 'QuestionController.php',
			$api_web_path . 'QuestionController.php',
			//routes
			$routes_path . 'api_question.php',
			$routes_path . 'web_b_question.php',
			//数据库
			$database_path . '2018_07_06_000000_create_ques_textinfo_table.php',
			$database_path . '2018_07_06_000000_create_quesinfo_list_table.php',
			$database_path . '2018_07_06_000000_create_quesinfo_option_table.php',
			$database_path . '2018_07_06_000000_create_queslist_table.php',
			//模型
			$model_path . 'QuesinfoList.php',
			$model_path . 'QuesinfoOption.php',
			$model_path . 'Queslist.php',
			$model_path . 'QuesTextinfo.php',
			//admin
			$web_path . 'QuestionController.php',
			//view
			$view_path . 'edit_ques.blade.php',
			$view_path . 'ques_list.blade.php',
			$view_path . 'ques_textinfo.blade.php',
			$view_path . 'quesinfo_list.blade.php',
			$view_path . 'ques_info.blade.php',
			$api_view_path . 'ques_html_end.blade.php',
			$api_view_path . 'ques_html_start.blade.php',
			//menu
			$menu_path . 'question_menu.php',
		];

		if ($type == 'check') {
			//文件检测
			$arr = [
				'name' => '问卷调查模块',
				'key' => 'Question',
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
			//删除相关数据库
			$arr = [
				[
					'tableName' => 'quesinfo_list',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'quesinfo_option',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'queslist',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'ques_textinfo',
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
