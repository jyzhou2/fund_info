<?php

namespace App\Http\Controllers\Admin\Positions;

use App\Dao\Load\PositionsDao;
use App\Models\Autonum;
use App\Models\Rent;
use App\Models\SvgMapTable;
use App\Models\TrajectoryList;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Support\Facades\DB;
use App\Models\Positions;
use App\Models\Users;

class PositionsController extends BaseAdminController
{
	//定位页面可显示的
	private $show_type = [
		'a' => true,
		//安卓
		'i' => true,
		//IOS
		'w' => true,
		//微信
		'sd' => true,
		//第十代智慧导览机
		'gq' => true,
		//智慧国七导览机
	];

	/**
	 * 定位列表页面
	 *
	 * @author yyj 20170906
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function positions_list($map_id = 0, $x = 0, $y = 0, $auto_num = 0, $keywords = '')
	{
		//获取地图信息
		$map_info = SvgMapTable::select('id as map_id', 'map_name as title', 'map_path')->orderBy('id', 'asc')->get();
		if (empty($map_info)) {
			echo '请添加地图';
			exit;
		}
		$map_info = $map_info->toArray();
		if (empty($map_id)) {
			$map_id = $map_info[0]['map_id'];
		}
		// 处理排序
		return view('admin.positions.positions_list', [
			'map_info' => $map_info,
			'map_id' => $map_id,
			'x' => $x,
			'y' => $y,
			'auto_num' => $auto_num,
			'keywords' => $keywords,
			'show_type' => $this->show_type
		]);
	}

	/**
	 * 定位数据获取
	 *
	 * @author yyj 20170906
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function ajax_map(Request $request)
	{
		//获取地图信息
		$map_id = $request->input('map_id');
		$map_info = SvgMapTable::where('id', $map_id)->first();
		$arr['map_path'] = $map_info->map_path;
		//获取关联点位信息
		$info = Autonum::where('map_id', $map_id)->select('autonum as auto_num', 'x as axis_x', 'y as axis_y')->get()->toArray();
		if (!empty($info)) {
			foreach ($info as $k => $g) {
				//人数统计
				$info[$k]['num'] = Positions::where('auto_num', $g['auto_num'])->count();
				if ($info[$k]['num'] == 0) {
					unset($info[$k]);
				}
			}
		} else {
			$info = [];
		}
		sort($info);
		$arr['count'] = count($info);
		$arr['pos_info'] = $info;
		return response()->json($arr);
	}

	/**
	 * 人员查询
	 *
	 * @author yyj 20170906
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function search(Request $request)
	{
		$keywords = $request->input('keywords');
		if (empty($keywords)) {
			return response()->json([
				'status' => 'error',
				'msg' => '请填写搜索条件'
			]);
		}
		//按租赁姓名查询
		$deviceno_arr = Rent::where('RENT_NAME', $keywords)->pluck('RENT_DEVICENO')->toArray();
		if (!empty($deviceno_arr)) {
			$info = Positions::whereIn('deviceno', $deviceno_arr)->select('x', 'y', 'map_id', 'auto_num')->first();
		}
		if (empty($info)) {
			$deviceno_arr = Rent::where('RENT_CARDNO', $keywords)->pluck('RENT_DEVICENO')->toArray();
			//按证件号查询是否存在
			$info = Positions::whereIn('deviceno', $deviceno_arr)->select('x', 'y', 'map_id', 'auto_num')->first();
		}
		if (empty($info)) {
			//按用户昵称查询
			$uid_arr = Users::where('nickname', 'like', '%' . $keywords . '%')->pluck('uid')->toArray();
			$info = Positions::whereIn('uid', $uid_arr)->select('x', 'y', 'map_id', 'auto_num')->first();
		}
		if (empty($info)) {
			//按用户账号查询
			$uid = Users::where('username', $keywords)->value('uid');
			$info = Positions::where('uid', $uid)->select('x', 'y', 'map_id', 'auto_num')->first();
		}
		if (empty($info)) {
			return response()->json([
				'status' => 'error',
				'msg' => '没有查到相关信息，请确认查询条件是否正确,是否开启定位服务'
			]);
		}
		//获取所在设备区域
		$arr['x'] = $info->x;
		$arr['y'] = $info->y;
		$arr['map_id'] = $info->map_id;
		$arr['auto_num'] = $info->auto_num;
		$arr['keywords'] = $keywords;
		$arr['code'] = 'success';
		return response()->json([
			'status' => 'success',
			'arr' => $arr
		]);
	}

	/**
	 * 点位详情查看
	 *
	 * @author yyj 20170906
	 * @return \Illuminate\Http\JsonResponse
	 */
	public function point(Request $request)
	{
		//讲解点定位详情
		//获取本地图设备编号上的人数信息
		$auto_num = $request->input('auto_num');
		if ($this->show_type['sd']) {
			//第十代智慧导览机
			$dlj1_arr = Positions::where('app_kind', 'd')->where('dlj_type', 1)->where('auto_num', $auto_num)->pluck('updated_at', 'deviceno')->toArray();
			$deviceno1_arr = array_keys($dlj1_arr);
			$info['dlj1'] = [];
			if (count($deviceno1_arr) > 0) {
				$dlj1_info = Rent::whereIn('RENT_DEVICENO', $deviceno1_arr)->pluck('RENT_NAME', 'RENT_DEVICENO')->toArray();
				foreach ($dlj1_arr as $k => $g) {
					if (isset($dlj1_info[$k])) {
						$info['dlj1'][] = [
							'rent_name' => $dlj1_info[$k],
							'deviceno' => $k,
							'datetime' => $g->format('Y-m-d H:i:s')
						];
					} else {
						$info['dlj1'][] = [
							'rent_name' => '设备未租赁',
							'deviceno' => $k,
							'datetime' => $g->format('Y-m-d H:i:s')
						];
					}
				}
			}
			$info['dlj1_num'] = count($info['dlj1']);
		}
		if ($this->show_type['gq']) {
			//获取智慧国七导览机
			$dlj2_arr = Positions::where('app_kind', 'd')->where('dlj_type', 2)->where('auto_num', $auto_num)->pluck('updated_at', 'deviceno')->toArray();
			$deviceno1_arr = array_keys($dlj2_arr);
			$info['dlj2'] = [];
			if (count($deviceno1_arr) > 0) {
				$dlj2_info = Rent::whereIn('RENT_DEVICENO', $deviceno1_arr)->pluck('RENT_NAME', 'RENT_DEVICENO')->toArray();
				foreach ($dlj2_arr as $k => $g) {
					if (isset($dlj2_info[$k])) {
						$info['dlj2'][] = [
							'rent_name' => $dlj2_info[$k],
							'deviceno' => $k,
							'datetime' => $g->format('Y-m-d H:i:s')
						];
					} else {
						$info['dlj2'][] = [
							'rent_name' => '设备未租赁',
							'deviceno' => $k,
							'datetime' => $g->format('Y-m-d H:i:s')
						];
					}
				}
			}
			$info['dlj2_num'] = count($info['dlj2']);
		}

		if ($this->show_type['a']) {
			//获取安卓数量
			$a_arr = Positions::where('app_kind', 'a')->where('auto_num', $auto_num)->select('updated_at', 'deviceno', 'uid')->get()->toArray();
			$info['a'] = [];
			if (count($a_arr) > 0) {
				foreach ($a_arr as $k => $g) {
					if ($g['uid']) {
						$user_info = Users::where('uid', $g['uid'])->select('nickname', 'username')->first();
						if (!empty($user_info)) {
							$info['a'][] = [
								'rent_name' => $user_info->nickname,
								'deviceno' => $user_info->username,
								'datetime' => $g['updated_at']
							];
						} else {
							$info['a'][] = [
								'rent_name' => '游客',
								'deviceno' => $g['deviceno'],
								'datetime' => $g['updated_at']
							];
						}
					} else {
						$info['a'][] = [
							'rent_name' => '游客',
							'deviceno' => $g['deviceno'],
							'datetime' => $g['updated_at']
						];
					}
				}
			}
			$info['a_num'] = count($info['a']);
		}
		if ($this->show_type['i']) {
			//获取IOS数量
			$i_arr = Positions::where('app_kind', 'i')->where('auto_num', $auto_num)->select('updated_at', 'deviceno', 'uid')->get()->toArray();
			$info['i'] = [];
			if (count($i_arr) > 0) {
				foreach ($i_arr as $k => $g) {
					if ($g['uid']) {
						$user_info = Users::where('uid', $g['uid'])->select('nickname', 'username')->first();
						if (!empty($user_info)) {
							$info['i'][] = [
								'rent_name' => $user_info->nickname,
								'deviceno' => $user_info->username,
								'datetime' => $g['updated_at']
							];
						} else {
							$info['i'][] = [
								'rent_name' => '游客',
								'deviceno' => $g['deviceno'],
								'datetime' => $g['updated_at']
							];
						}
					} else {
						$info['i'][] = [
							'rent_name' => '游客',
							'deviceno' => $g['deviceno'],
							'datetime' => $g['updated_at']
						];
					}
				}
			}
			$info['i_num'] = count($info['i']);
		}
		if ($this->show_type['w']) {
			//获取微信数量
			$w_arr = Positions::where('app_kind', 'w')->where('auto_num', $auto_num)->select('updated_at', 'deviceno', 'uid')->get()->toArray();
			$info['w'] = [];
			if (count($w_arr) > 0) {
				foreach ($w_arr as $k => $g) {
					if ($g['uid']) {
						$user_info = Users::where('uid', $g['uid'])->select('nickname', 'username')->first();
						if (!empty($user_info)) {
							$info['w'][] = [
								'rent_name' => $user_info->nickname,
								'deviceno' => $user_info->username,
								'datetime' => $g['updated_at']
							];
						} else {
							$info['w'][] = [
								'rent_name' => '游客',
								'deviceno' => $g['deviceno'],
								'datetime' => $g['updated_at']
							];
						}
					} else {
						$info['w'][] = [
							'rent_name' => '游客',
							'deviceno' => $g['deviceno'],
							'datetime' => $g['updated_at']
						];
					}
				}
			}
			$info['w_num'] = count($info['w']);
		}

		//获取点位名称
		$exhibit_name = Autonum::where('autonum', $auto_num)->value('exhibit_name');
		$exhibit_name = implode(',', json_decode($exhibit_name, true));
		if (mb_strlen($exhibit_name, 'utf-8') > 18) {
			$exhibit_name = mb_substr($exhibit_name, 0, 18, 'utf-8') . '...';
		}
		$info['exhibit_name'] = $exhibit_name;
		return response()->json($info);
	}

}
