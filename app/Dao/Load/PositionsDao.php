<?php

namespace App\Dao\Load;

use App\Dao\SettingDao;
use App\Models\Backup;
use App\Models\Heartbeat;
use App\Models\Positions;
use App\Models\Rent;
use App\Models\TrajectoryList;
use App\Models\UserAttributes;

/**
 * 观众定位模块安装模型
 *
 * @author yyj 20180301
 */
class PositionsDao extends LoadDao
{

	/**
	 * 观众定位模块安装模型
	 *
	 * @author yyj 20180301
	 * @param string $type 要执行的命令
	 * @return bool|array
	 */
	public static function positions_controller($type)
	{
		$load_file_path = base_path() . '/load_file/positions/';
		$database_path = base_path() . '/database/migrations/';
		$model_path = base_path() . '/app/Models/';
		$dao_path = base_path() . '/app/Dao/';
		$api_path = base_path() . '/app/Http/Controllers/Api/';
		$api_and_path = base_path() . '/app/Http/Controllers/Api/Android/';
		$api_ios_path = base_path() . '/app/Http/Controllers/Api/Ios/';
		$api_web_path = base_path() . '/app/Http/Controllers/Api/Web/';
		$api_dlj_path = base_path() . '/app/Http/Controllers/Api/Dlj/';
		$routes_path = base_path() . '/routes/';
		$menu_path = base_path() . '/config/load_menu/';

		$web_path = base_path() . '/app/Http/Controllers/Admin/Positions/';
		$view_path = base_path() . '/resources/views/admin/positions/';
		$api_view_path = base_path() . '/resources/views/api/positions/';

		//文件装载列表
		$load_file_list = [
			//api
			$load_file_path . 'DevicenoController.php',
			$load_file_path . 'AndDevicenoController.php',
			$load_file_path . 'IosDevicenoController.php',
			$load_file_path . 'WebDevicenoController.php',
			$load_file_path . 'DljDevicenoController.php',
			//routes
			$load_file_path . 'api_deviceno.php',
			$load_file_path . 'web_positions.php',
			//数据库
			$load_file_path . '2018_07_20_000000_create_deviceno_table.php',
			$load_file_path . '2018_07_20_000000_create_heartbeat_table.php',
			$load_file_path . '2018_07_20_000000_create_positions_table.php',
			$load_file_path . '2018_07_20_000000_create_trajectory_table.php',
			$load_file_path . '2018_07_20_000000_create_trajectory_list_table.php',
			$load_file_path . '2018_08_09_000000_create_user_attributes_table.php',
			//模型
			$load_file_path . 'BackUp.php',
			$load_file_path . 'Deviceno.php',
			$load_file_path . 'Heartbeat.php',
			$load_file_path . 'Positions.php',
			$load_file_path . 'Rent.php',
			$load_file_path . 'Trajectory.php',
			$load_file_path . 'TrajectoryList.php',
			$load_file_path . 'UserAttributes.php',
			//admin
			$load_file_path . 'AdminPositionsController.php',
			$load_file_path . 'AdminTrajectoryController.php',
			//view
			$load_file_path . 'backup_trajectory_info.blade.php',
			$load_file_path . 'backup_trajectory_list.blade.php',
			$load_file_path . 'user_trajectory_info.blade.php',
			$load_file_path . 'user_trajectory_list.blade.php',
			$load_file_path . 'positions_list.blade.php',
			$load_file_path . 'rent_trajectory_info.blade.php',
			$load_file_path . 'rent_trajectory_list.blade.php',
			//menu
			$load_file_path . 'positions_menu.php',
		];

		//安装列表
		$install_file_list = [
			//api
			$api_path . 'DevicenoController.php',
			$api_and_path . 'DevicenoController.php',
			$api_ios_path . 'DevicenoController.php',
			$api_web_path . 'DevicenoController.php',
			$api_dlj_path . 'DevicenoController.php',
			//routes
			$routes_path . 'api_deviceno.php',
			$routes_path . 'web_b_positions.php',
			//数据库
			$database_path . '2018_07_20_000000_create_deviceno_table.php',
			$database_path . '2018_07_20_000000_create_heartbeat_table.php',
			$database_path . '2018_07_20_000000_create_positions_table.php',
			$database_path . '2018_07_20_000000_create_trajectory_table.php',
			$database_path . '2018_07_20_000000_create_trajectory_list_table.php',
			$database_path . '2018_08_09_000000_create_user_attributes_table.php',
			//模型
			$model_path . 'BackUp.php',
			$model_path . 'Deviceno.php',
			$model_path . 'Heartbeat.php',
			$model_path . 'Positions.php',
			$model_path . 'Rent.php',
			$model_path . 'Trajectory.php',
			$model_path . 'TrajectoryList.php',
			$model_path . 'UserAttributes.php',
			//admin
			$web_path . 'PositionsController.php',
			$web_path . 'TrajectoryController.php',
			//view
			$view_path . 'backup_trajectory_info.blade.php',
			$view_path . 'backup_trajectory_list.blade.php',
			$view_path . 'user_trajectory_info.blade.php',
			$view_path . 'user_trajectory_list.blade.php',
			$view_path . 'positions_list.blade.php',
			$view_path . 'rent_trajectory_info.blade.php',
			$view_path . 'rent_trajectory_list.blade.php',
			//menu
			$menu_path . 'positions_menu.php',
		];

		if ($type == 'check') {
			//文件检测
			$arr = [
				'name' => '观众定位',
				'key' => 'Positions',
				'status' => 0,
				'des' => '需先安装SVG地图管理模块,展品相关管理模块'
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
			//删除相关数据库
			$arr = [
				[
					'tableName' => 'positions',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'deviceno',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'heartbeat',
					'primaryKey' => 'id'
				],
				[
					'tableName' => 'trajectory',
					'primaryKey' => 'id'
				],
				[
					'tableName'=>'trajectory_list',
					'primaryKey' => 'id'
				],
				[
					'tableName'=>'user_attributes',
					'primaryKey' => 'id'
				]
			];
			self::drop_table($arr);
			LoadDao::create_table_api(0, 1);
			return true;
		} else {
			return false;
		}
	}

	/*
	 * 过期定位点清除
	 *
	 * */
	public static function handle(){
		$model_path = base_path() . '/app/Models/';
		if(file_exists($model_path . 'Heartbeat.php')&&file_exists($model_path . 'Positions.php')){
			//清除150内没有心跳交互的过期数据
			$heart_arr=Heartbeat::where('updated_at','<',date('Y-m-d H:i:s', time() - 150))->pluck('deviceno')->toArray();
			/*var_dump($heart_arr);*/
			//删除相关的心跳和定位数据
			Heartbeat::whereIn('deviceno',$heart_arr)->delete();
			Positions::whereIn('deviceno',$heart_arr)->where('app_kind','d')->where('dlj_type',3)->delete();
			//清除已归还导览机的定位数据
			$deviceno_arr=Rent::pluck('RENT_DEVICENO')->toArray();
			Positions::whereNotIn('deviceno',$deviceno_arr)->where('app_kind','d')->where('dlj_type','<>',3)->delete();
			//更改已归还导览机的状态
			$rent_id_arr=Rent::pluck('RENT_ID')->toArray();
			$list=TrajectoryList::where('rent_type',1)->whereNotIn('rent_id',$rent_id_arr)->select('deviceno','card_id','rent_time','id')->get()->toArray();
			foreach ($list as $k=>$g){
				$backup_time=Backup::where('BACKUP_CARDNO',$g['card_id'])->where('BACKUP_DEVICENO',$g['deviceno'])->where('BACKUP_STARTTIME',$g['rent_time'])->value('BACKUP_ENDTIME');
				TrajectoryList::where('id',$g['id'])->update(['back_time'=>$backup_time,'rent_type'=>2]);
			}
			//获取已记录的租赁编号
			$record_rent_id=SettingDao::getSetting('record_rent_id');
			if(empty($record_rent_id)){
				$record_rent_id=0;
			}
			//获取需要记录的身份证号
			$card_id_arr=Rent::where('RENT_ID','>',$record_rent_id)->orderBy('RENT_ID','asc')->pluck('RENT_CARDNO','RENT_ID')->toArray();
			$data=[];
			foreach ($card_id_arr as $k=>$g){
				$record_rent_id=$k;
				if(is_idcard($g,true)){
					$data[]=[
						'age'=>date('Y') - substr($g, 6, 4),
						'sex'=>substr($g, 16, 1) % 2,
						'province' => substr($g, 0, 2),
						'city' => substr($g, 0, 4),
						'area' => substr($g, 0, 6),
					];
				}
			}
			if(count($data)){
				UserAttributes::insert($data);
				SettingDao::setSetting('record_rent_id',$record_rent_id);
			}
		}
	}
}
