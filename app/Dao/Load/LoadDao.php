<?php

namespace App\Dao\Load;

use App\Models\Migrations;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use App\Models\UploadedType;

/**
 * 一键安装模型
 *
 * @author yyj 20171213
 */
class LoadDao
{

	//数据库卸载
	protected static function drop_table($arr)
	{
		foreach ($arr as $k => $g) {
			Schema::dropIfExists($g['tableName']);
			if (env('DB_CONNECTION') == 'oracle') {
				$sequence = DB::getSequence();
				$sequence->drop(strtoupper($g['tableName'] . '_' . $g['primaryKey'] . '_SEQ'));
			}
			Migrations::where('migration','like','%000000_create_'.$g['tableName'].'_table')->delete();
		}
	}

	/**
	 * 文件检测
	 * @param  array $arr 模块属性
	 * @param array $load_file_list 基础安装列表文件
	 * @param array $install_file_list 安装列表文件
	 * @return array
	 * @author yyj 20180303
	 */
	protected static function check_file($arr,$load_file_list,$install_file_list){
		$load_file_num = 0;
		$file_num = 0;
		$load_file_des = '';
		$file_des = '';
		foreach ($load_file_list as $k => $g) {
			$load_file_num = $load_file_num + 1;
			if (!file_exists($g)) {
				$load_file_des = $load_file_des . '基础安装文件'.$g . '缺失<br />';
			} else {
				if (!file_exists($install_file_list[$k])) {
					$file_des = $file_des . '功能模块文件'.$install_file_list[$k] . '缺失<br />';
				} else {
					if(md5(file_get_contents($g))!==md5(file_get_contents($install_file_list[$k]))){
						$arr['status'] = 4;
						$file_des = $file_des .'功能模块文件'. $install_file_list[$k] . '与基础安装文件不符<br />';
					}
					else{
						$file_num = $file_num + 1;
					}
				}
			}
		}

		if (!empty($load_file_des)) {
			$arr['status'] = 2;
			$arr['des'] = $load_file_des;
		} elseif ($file_num == 0) {
			$arr['status'] = 0;
		} elseif ($file_num != $load_file_num&&$arr['status']==0) {
			$arr['status'] = 3;
			$arr['des'] = $file_des;
		} elseif ($file_num != $load_file_num&&$arr['status']!=0) {
			$arr['des'] = $file_des;
		}
		else{
			$arr['status'] = 1;
			$arr['des'] = '';
		}
		return $arr;
	}

	/*
	 * 生成数据库及接口文档
	 *
	 * */
	public static function create_table_api($is_table,$is_api){
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			//echo '这个服务器操作系统为Windows!';
			if($is_table){
				exec('cd ' . base_path() . ' &&php artisan migrate');
			}
			if($is_api){
				exec('cd ' . base_path() . ' &&php artisan apidoc');
			}
		}else{
			//echo '这个服务器操作系统不是Windows系统!';
			if($is_table){
				exec('cd ' . base_path() . ' &&sudo php artisan migrate');
			}
			if($is_api){
				exec('cd ' . base_path() . ' &&sudo php artisan apidoc');
			}
		}
	}

	/*
	 * 一键安装模块卸载
	 *
	 *
	 * */
	public static function uninstall(){
		$dir_arr=[
			'load_file_path'=>base_path() . '/load_file/',
			'load_view_path'=>base_path() . '/resource/views/admin/load/',
			'load_controller_path'=>base_path() . '/app/Http/Controller/Admin/Load/',
		];
		$file_arr=[
			'load_menu_path'=>base_path(). '/config/load_menu/load_menu.php',
			'LoadExhibitDao_path'=>base_path() . '/app/Dao/Load/LoadExhibitDao.php',
			'LoadServiceDao_path'=>base_path() . '/app/Dao/Load/LoadServiceDao.php',
			'PaiDao_path'=>base_path() . '/app/Dao/Load/PaiDao.php',
			'SvgMapDao_path'=>base_path() . '/app/Dao/Load/SvgMapDao.php',
			'NavigationRoadDao_path'=>base_path() . '/app/Dao/Load/NavigationRoadDao.php',
			'ViewGuideDao_path'=>base_path() . '/app/Dao/Load/ViewGuideDao.php',
			'QuestionDao_path'=>base_path() . '/app/Dao/Load/QuestionDao.php',
			'PositionsDao_path'=>base_path() . '/app/Dao/Load/PositionsDao.php',
			'web_load_path'=>base_path() . '/routes/web_load.php',
		];

		foreach ($dir_arr as $k=>$g){
			if(file_exists($g)){
				deldir($g);
			}
		}

		foreach ($file_arr as $k=>$g){
			if(file_exists($g)){
				unlink($g);
			}
		}

	}
}
