<?php

namespace App\Http\Controllers\Admin\Positions;

use App\Dao\Load\PositionsDao;
use App\Models\Rent;
use App\Models\SvgMapTable;
use App\Models\TrajectoryList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Support\Facades\DB;
use \Illuminate\Support\Facades\Validator;
use App\Models\Trajectory;
use App\Models\Positions;
use App\Models\Users;

class TrajectoryController extends BaseAdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	/*
	 * 租赁中的轨迹列表
	 * @author yyj 20180723
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * */
	public function rent_trajectory_list()
	{
		$query = TrajectoryList::where('rent_type', 1)->where('app_kind', 'd')->orderBy('rent_time', 'desc');
		//设备类型
		if (request('dlj_type')) {
			$query->where('dlj_type', request('dlj_type'));
		}
		// 租赁者姓名
		if (request('rent_name')) {
			$query->where('rent_name', 'LIKE', "%" . request('rent_name') . "%");
		}
		// 租赁者证件号
		if (request('rent_cardno')) {
			$query->where('card_id', request('rent_cardno'));
		}
		// 设备编号
		if (request('deviceno')) {
			$query->where('deviceno', request('deviceno'));
		}
		// 筛选发表时间时间
		if (request('created_at')) {
			list($begin, $end) = explode(' ~ ', request('created_at'));
			$query->whereBetween('rent_time', [
				date('Y-m-d H:i:s', strtotime($begin)),
				date('Y-m-d H:i:s', strtotime('+1 day', strtotime($end)))
			]);
		}

		$info = $query->select('rent_name as RENT_NAME', 'card_id as RENT_CARDNO', 'rent_time as RENT_STARTTIME', 'deviceno as RENT_DEVICENO', 'rent_id as RENT_ID', 'dlj_type')->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());
		return view('admin.positions.rent_trajectory_list', [
			'info' => $info
		]);
	}

	/*
	 * 轨迹详情
	 * @author yyj 20180723
     * @return \Illuminate\Http\JsonResponse
	 * */
	public function rent_trajectory_info($rent_id, $map_id = 0)
	{
		//获取租赁者信息
		$rent_info = TrajectoryList::where('rent_type', 1)->where('rent_id', $rent_id)->select('rent_name as RENT_NAME', 'rent_time as RENT_STARTTIME', 'deviceno as RENT_DEVICENO')->first();
		if (empty($rent_info)) {
			return $this->error('页面数据错误');
		}
		//获取有数据的map_id
		$map_id_arr = Trajectory::where('rent_id', $rent_id)->groupBy('map_id')->pluck('map_id')->toArray();
		//获取地图数据
		$map_info = SvgMapTable::whereIn('id', $map_id_arr)->select('id as map_id', 'map_name', 'map_path')->get()->toArray();
		if ($map_id == 0) {
			$map_id = $map_info[0]['map_id'];
		}
		//获取坐标详情
		$axis_info = Trajectory::where('rent_id', $rent_id)->where('map_id', $map_id)->orderBy('created_at')->select('auto_num', 'x', 'y', 'created_at')->get()->toArray();
		$num = count($axis_info);
		$json_info = '[';
		for ($i = 0; $i < $num; $i++) {
			if ($i + 2 <= $num) {
				$json_info = $json_info . '[{name:"' . $axis_info[$i]['created_at'] . '",geoCoord:[' . $axis_info[$i]['x'] . ',' . $axis_info[$i]['y'] . ']},{name:"' . $axis_info[$i + 1]['created_at'] . '",geoCoord:[' . $axis_info[$i + 1]['x'] . ',' . $axis_info[$i + 1]['y'] . ']}],';
			}
		}
		$json_info = $json_info . ']';
		$gj_info['map_json'] = $json_info;

		$gj_info['path'] = SvgMapTable::where('id', $map_id)->value('map_path');
		return view('admin.positions.rent_trajectory_info', [
			'map_info' => $map_info,
			'gj_info' => $gj_info,
			'map_id' => $map_id,
			'rent_id' => $rent_id,
			'rent_info' => $rent_info
		]);
	}

	/*
	 * 已归还的轨迹列表
	 * @author yyj 20180723
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * */
	public function backup_trajectory_list()
	{
		//获取已归还的数据
		$query = TrajectoryList::where('rent_type', 2)->where('app_kind', 'd')->orderBy('rent_time', 'desc');
		//设备类型
		if (request('dlj_type')) {
			$query->where('dlj_type', request('dlj_type'));
		}
		// 租赁者姓名
		if (request('rent_name')) {
			$query->where('rent_name', 'LIKE', "%" . request('rent_name') . "%");
		}
		// 租赁者证件号
		if (request('rent_cardno')) {
			$query->where('card_id', request('rent_cardno'));
		}
		// 设备编号
		if (request('deviceno')) {
			$query->where('deviceno', request('deviceno'));
		}
		// 筛选发表时间时间
		if (request('created_at')) {
			list($begin, $end) = explode(' ~ ', request('created_at'));
			$query->whereBetween('rent_time', [
				date('Y-m-d H:i:s', strtotime($begin)),
				date('Y-m-d H:i:s', strtotime('+1 day', strtotime($end)))
			]);
		}
		$info = $query->select('rent_name as RENT_NAME', 'card_id as RENT_CARDNO', 'rent_time as RENT_STARTTIME', 'deviceno as RENT_DEVICENO', 'rent_id as RENT_ID', 'back_time', 'dlj_type')->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());
		return view('admin.positions.backup_trajectory_list', [
			'info' => $info
		]);
	}

	/*
	 * 轨迹详情
	 * @author yyj 20180723
     * @return \Illuminate\Http\JsonResponse
	 * */
	public function backup_trajectory_info($rent_id, $map_id = 0)
	{
		//获取租赁者信息
		$rent_info = TrajectoryList::where('rent_type', 2)->where('rent_id', $rent_id)->select('rent_name as RENT_NAME', 'rent_time as RENT_STARTTIME', 'deviceno as RENT_DEVICENO', 'back_time')->first();
		if (empty($rent_info)) {
			return $this->error('页面数据错误');
		}
		//获取有数据的map_id
		$map_id_arr = Trajectory::where('rent_id', $rent_id)->groupBy('map_id')->pluck('map_id')->toArray();
		//获取地图数据
		$map_info = SvgMapTable::whereIn('id', $map_id_arr)->select('id as map_id', 'map_name', 'map_path')->get()->toArray();
		if ($map_id == 0) {
			if(empty($map_info)||!isset($map_info[0]['map_id'])){
				return $this->error('暂无轨迹数据');
			}
			$map_id = $map_info[0]['map_id'];
		}
		//获取坐标详情
		$axis_info = Trajectory::where('rent_id', $rent_id)->where('map_id', $map_id)->orderBy('created_at')->select('auto_num', 'x', 'y', 'created_at')->get()->toArray();
		$num = count($axis_info);
		$json_info = '[';
		for ($i = 0; $i < $num; $i++) {
			if ($i + 2 <= $num) {
				$json_info = $json_info . '[{name:"' . $axis_info[$i]['created_at'] . '",geoCoord:[' . $axis_info[$i]['x'] . ',' . $axis_info[$i]['y'] . ']},{name:"' . $axis_info[$i + 1]['created_at'] . '",geoCoord:[' . $axis_info[$i + 1]['x'] . ',' . $axis_info[$i + 1]['y'] . ']}],';
			}
		}
		$json_info = $json_info . ']';
		$gj_info['map_json'] = $json_info;

		$gj_info['path'] = SvgMapTable::where('id', $map_id)->value('map_path');
		return view('admin.positions.backup_trajectory_info', [
			'map_info' => $map_info,
			'gj_info' => $gj_info,
			'map_id' => $map_id,
			'rent_id' => $rent_id,
			'rent_info' => $rent_info
		]);
	}

	/*
	 * 用户轨迹列表
	 * @author yyj 20180723
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * */
	public function user_trajectory_list()
	{
		//获取已归还的数据
		$query = TrajectoryList::leftJoin('users', 'users.uid', '=', 'trajectory_list.uid')->where('trajectory_list.dlj_type', 0)->orderBy('trajectory_list.look_date', 'desc');

		// 账号/昵称
		if (request('deviceno')) {
			$uid_arr = Users::where('username', 'like', '%' . request('deviceno') . '%')->orwhere('nickname', 'like', '%' . request('deviceno') . '%')->pluck('uid')->toArray();
			if (count($uid_arr)) {
				$query->whereIn('trajectory_list.uid', $uid_arr);
			} else {
				$query->where('trajectory_list.id', 0);
			}
		}
		// 筛选发表时间时间
		if (request('created_at')) {
			list($begin, $end) = explode(' ~ ', request('created_at'));
			$query->whereBetween('trajectory_list.look_date', [
				date('Y-m-d H:i:s', strtotime($begin)),
				date('Y-m-d H:i:s', strtotime('+1 day', strtotime($end)))
			]);
		}
		$info = $query->select('trajectory_list.look_date', 'trajectory_list.uid', 'users.username', 'users.nickname', 'users.avatar')->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());
		return view('admin.positions.user_trajectory_list', [
			'info' => $info
		]);
	}

	/*
	 * 轨迹详情
	 * @author yyj 20180723
	 * @return \Illuminate\Http\JsonResponse
	 * */
	public function user_trajectory_info($uid, $look_date, $map_id = 0)
	{
		$rent_name = request('rent_name');
		//获取有数据的map_id
		$map_id_arr = Trajectory::where('uid', $uid)->where('look_date', $look_date)->where('dlj_type', 0)->groupBy('map_id')->pluck('map_id')->toArray();
		//获取地图数据
		$map_info = SvgMapTable::whereIn('id', $map_id_arr)->select('id as map_id', 'map_name', 'map_path')->get()->toArray();
		if ($map_id == 0) {
			if(empty($map_info)||!isset($map_info[0]['map_id'])){
				return $this->error('暂无轨迹数据');
			}
			$map_id = $map_info[0]['map_id'];
		}
		//获取坐标详情
		$axis_info = Trajectory::where('uid', $uid)->where('look_date', $look_date)->where('dlj_type', 0)->where('map_id', $map_id)->orderBy('created_at')->select('auto_num', 'x', 'y', 'created_at')->get()->toArray();
		$num = count($axis_info);
		$json_info = '[';
		for ($i = 0; $i < $num; $i++) {
			if ($i + 2 <= $num) {
				$json_info = $json_info . '[{name:"' . $axis_info[$i]['created_at'] . '",geoCoord:[' . $axis_info[$i]['x'] . ',' . $axis_info[$i]['y'] . ']},{name:"' . $axis_info[$i + 1]['created_at'] . '",geoCoord:[' . $axis_info[$i + 1]['x'] . ',' . $axis_info[$i + 1]['y'] . ']}],';
			}
		}
		$json_info = $json_info . ']';
		$gj_info['map_json'] = $json_info;

		$gj_info['path'] = SvgMapTable::where('id', $map_id)->value('map_path');
		return view('admin.positions.user_trajectory_info', [
			'map_info' => $map_info,
			'gj_info' => $gj_info,
			'map_id' => $map_id,
			'uid' => $uid,
			'look_date' => $look_date,
			'rent_name' => $rent_name
		]);
	}
}
