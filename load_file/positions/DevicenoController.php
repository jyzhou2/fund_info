<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiErrorException;
use App\Models\Autonum;
use App\Models\ExUserVisit;
use App\Models\Rent;
use App\Models\TrajectoryList;
use Illuminate\Support\Facades\Auth;
use App\Models\Deviceno;
use App\Models\Heartbeat;
use App\Models\Positions;
use App\Models\Trajectory;
use Illuminate\Support\Facades\DB;

/**
 * 设备号相关控制器
 *
 * @author yyj
 * @package App\Http\Controllers\Api
 */
class DevicenoController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 机器号请求接口
	 *
	 * @author yyj 20171110
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /request_deviceno 01.机器号请求接口(导览机忽略此接口)
	 * @apiGroup Deviceno(yyj)
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信（app存本地一直用到卸载，微信存cookie失效后再重新请求）
	 * @apiSuccess {string} data 机器号
	 * @apiSuccessExample {json} 返回值
	 * {
	 * "status": 1,
	 * "data": "AND1000000002",
	 * "msg": ""
	 * }
	 */
	public function request_deviceno()
	{
		$this->validate([
			'p' => 'required|in:i,a,w',
		]);
		$app_kind = request('p');
		//判断注册设备类型
		switch ($app_kind) {
			case 'a':
				$deviceno = 'AND';
				break;
			case 'i':
				$deviceno = 'IOS';
				break;
			case 'w':
				$deviceno = 'WEB';
				break;
		}
		$info = Deviceno::create([
			'deviceno' => time(),
			'app_kind' => $app_kind
		]);
		$deviceno = $deviceno . ($info['id'] + 1000000000);
		Deviceno::where('id', $info['id'])->update(['deviceno' => $deviceno]);
		return response_json(1, $deviceno);
	}

	/**
	 * 心跳响应接口
	 *
	 * @author yyj 20170809
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /heartbeat 02.心跳响应接口2分钟上传一次(导览机忽略此接口)
	 * @apiGroup Deviceno(yyj)
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓、K4,w:微信
	 * @apiParam {string} deviceno 机器号
	 * @apiParam {string} [api_token] token(登录后上传)
	 * @apiSuccess {string} data 操作结果
	 * @apiSuccessExample {json} 返回值
	 * {
	 * "status": 1,
	 * "data": 1,
	 * "msg": ""
	 * }
	 */
	public function heartbeat()
	{
		$this->validate([
			'p' => 'required|in:i,a,w',
			'deviceno' => 'required|string|min:13|max:13'
		]);
		$app_kind = request('p');
		$deviceno = request('deviceno');

		$info = Heartbeat::where('heart_date', date('Y-m-d', time()))->where('deviceno', $deviceno)->where('app_kind', $app_kind)->first();
		if (empty($info)) {
			Heartbeat::create([
				'deviceno' => $deviceno,
				'app_kind' => $app_kind,
				'heart_date' => date('Y-m-d', time())
			]);
		} else {
			Heartbeat::where('id', $info->id)->update([
				'deviceno' => $deviceno,
				'app_kind' => $app_kind,
				'heart_date' => date('Y-m-d', time())
			]);
		}

		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
			$u_ex_info = ExUserVisit::where('uid', $uid)->first();
			if (empty($u_ex_info)) {
				ExUserVisit::create([
					'uid' => $uid,
					'use_time' => 120
				]);
			} else {
				$use_time = time() - strtotime($u_ex_info->updated_at);
				if ($use_time >= 120) {
					ExUserVisit::where('uid', $uid)->increment('use_time', 120);
				} else {
					ExUserVisit::where('uid', $uid)->increment('use_time', $use_time);
				}
			}
		}

		return response_json(1, 1);
	}

	/**
	 * 定位上传接口
	 *
	 * @author yyj 20170809
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /positions 03.定位上传接口
	 * @apiGroup Deviceno(yyj)
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信,d:导览机
	 * @apiParam {string} deviceno 机器号
	 * @apiParam {int} auto_num 蓝牙编号
	 * @apiParam {int} [dlj_type] 导览机类型p=d时必传，1:十代机,2:欧7
	 * @apiParam {string} [api_token] token(登录后上传)
	 * @apiSuccess {string} data 展厅拥挤详情
	 * @apiSuccess {array} in_exhibition 当前所在展厅
	 * @apiSuccess {array} out_exhibition 其他展厅
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} exhibition_address 展厅地址
	 * @apiSuccess {string} exhibition_img 展厅图片
	 * @apiSuccess {int} exhibition_id 展厅id
	 * @apiSuccess {int} type 是否拥挤1拥挤0不拥挤
	 * @apiSuccess {int} now_num 展厅当前人数
	 */
	public function positions()
	{
		$this->validate([
			'p' => 'required|in:i,a,w,d',
			'deviceno' => 'required|string|min:13|max:13',
			'auto_num' => 'required|integer',
			'dlj_type' => 'nullable|int|in:1,2'
		]);
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
		} else {
			$uid = 0;
		}
		$deviceno = request('deviceno');
		$auto_num = request('auto_num');
		$app_kind = request('p');
		$dlj_type = request('dlj_type', 0);
		if ($app_kind == 'd' && empty($dlj_type)) {
			throw new ApiErrorException('导览机类型不能为空');
		}
		//判断点位是否存在
		$autonum_info = Autonum::where('autonum', $auto_num)->select('map_id', 'x', 'y')->first();
		if (empty($autonum_info)) {
			throw new ApiErrorException('auto_num不存在');
		}

		//判断数据更新方法
		$is_set2 = Positions::where('deviceno', $deviceno)->where('app_kind', $app_kind)->value('id');
		$data['deviceno'] = $deviceno;
		$data['auto_num'] = $auto_num;
		$data['app_kind'] = $app_kind;
		$data['dlj_type'] = $dlj_type;
		$data['map_id'] = $autonum_info->map_id;
		$data['x'] = $autonum_info->x;
		$data['y'] = $autonum_info->y;
		$data['uid'] = $uid;
		if (empty($is_set2)) {
			Positions::create($data);
		} else {
			Positions::where('id', $is_set2)->update($data);
		}
		$add = false;
		if ($app_kind == 'd') {
			//租赁设备轨迹处理
			//判断是否租赁
			$deviceno_info = Rent::where('RENT_DEVICENO', $deviceno)->first();
			if (!empty($deviceno_info)) {
				$rent_id = $deviceno_info->RENT_ID;
				//判断是否存在轨迹列表中
				$trajctory_list_info = TrajectoryList::where('deviceno', $deviceno_info->RENT_DEVICENO)->where('card_id', $deviceno_info->RENT_CARDNO)->where('rent_time', $deviceno_info->RENT_STARTTIME)->first();
				if (empty($trajctory_list_info)) {
					//创建轨迹列表
					TrajectoryList::create([
						'deviceno' => $deviceno,
						'app_kind' => $app_kind,
						'dlj_type' => $dlj_type,
						'card_id' => $deviceno_info->RENT_CARDNO,
						'rent_id' => $rent_id,
						'rent_time' => $deviceno_info->RENT_STARTTIME,
						'uid' => 0,
						'rent_name' => $deviceno_info->RENT_NAME,
						'look_date' => date('Y-m-d', time())
					]);
				}
				$data['rent_id'] = $rent_id;
				$data['look_date'] = date('Y-m-d', time());
				$data['card_id'] = $deviceno_info->RENT_CARDNO;
				//隔一复收判断
				$last_positions = Trajectory::where('rent_id', $rent_id)->orderBy('id','desc')->first();
				if (!empty($last_positions)) {
					if ($last_positions->auto_num != $auto_num) {
						$add = true;
					}
				} else {
					$add = true;
				}
			}
		} else {
			//判断是否登录
			if ($uid) {
				//判断是否存在轨迹列表中
				$trajctory_list_info = TrajectoryList::where('uid', $uid)->where('look_date', date('Y-m-d', time()))->first();
				if (empty($trajctory_list_info)) {
					//创建轨迹列表
					TrajectoryList::create([
						'deviceno' => $deviceno,
						'app_kind' => $app_kind,
						'dlj_type' => $dlj_type,
						'card_id' => 0,
						'rent_id' => 0,
						'rent_time' => date('Y-m-d H:i:s', time()),
						'uid' => $uid,
						'look_date' => date('Y-m-d', time())
					]);
				}

				$data['look_date'] = date('Y-m-d', time());
				$data['uid'] = $uid;
				//隔一复收判断
				$last_positions = Trajectory::where('uid', $uid)->orderBy('id','desc')->where('look_date', date('Y-m-d', time()))->first();
				if (!empty($last_positions)) {
					if ($last_positions->auto_num != $auto_num) {
						$add = true;
					}
				} else {
					$add = true;
				}
			}
		}
		if ($add) {
			//将数据写入轨迹详情
			Trajectory::create($data);
		}
		return response_json(1, []);
	}

}