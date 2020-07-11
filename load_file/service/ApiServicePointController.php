<?php

namespace App\Http\Controllers\Api;

use App\Models\ServicePoint;

/**
 * 展品导览相关接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class ServicePointController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 获取地图页服务设施点位
	 *
	 * @author yyj 20171112
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /map_service_point 获取地图页服务设施点位
	 * @apiGroup Service_point
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} map_id 地图编号传
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} img 设施图片
	 * @apiSuccess {int} map_id 地图编号
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 *
	 */
	public function map_service_point()
	{
		$map_id = request('map_id');
		$data = ServicePoint::where('map_id', $map_id)->select('map_id', 'x', 'y', 'img')->get();
		foreach ($data as $k=>$g){
			$data[$k]['img']=get_file_url($g['img']);
		}
		return response_json(1, $data);
	}
}