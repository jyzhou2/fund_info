<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\ApiErrorException;
use App\Models\SvgMapTable;
use Illuminate\Support\Facades\Auth;

class SvgMapController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 地图列表
	 *
	 * @author yyj 20171113
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /map_list 地图列表
	 * @apiGroup SvgMap
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {string} floor_id 楼层id,floor_id为0则返回所有地图数据
	 * @apiSuccess {array} data 数据详情
	 * @apiSuccess {int} map_id 地图编号
	 * @apiSuccess {int} floor_id 楼层编号
	 * @apiSuccess {int} width 地图宽度
	 * @apiSuccess {int} height 地图高度
	 * @apiSuccess {string} map_name 地图名称
	 * @apiSuccess {string} map_path 瓦片地图地址
	 * @apiSuccess {string} road_path 路线图片地址
	 */
	public function map_list()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'floor_id' => 'required|min:0|integer',
		]);
		$language = request('language', 0);
		$floor_id = request('floor_id', 10);
		// 取得当前楼层地图数据
		if ($floor_id == 0) {
			$map_list = SvgMapTable::select('id as map_id', 'floor_id', 'width', 'height', 'map_name_json')->get()->toArray();
		} else {
			$map_list = SvgMapTable::where('floor_id', $floor_id)->select('id as map_id', 'floor_id', 'width', 'height', 'map_name_json')->get()->toArray();
		}
		foreach ($map_list as $k => $g) {
			$map_list[$k]['map_name'] = json_decode($g['map_name_json'], true)[$language];
			unset($map_list[$k]['map_name_json']);
			$map_list[$k]['map_path']=get_file_url('/resource_zip/map/'.$g['map_id'].'/');
			$map_list[$k]['road_path']=get_file_url('/resource_zip/map/road'.$g['map_id'].'.png');
		}
		return response_json(1, $map_list);
	}

}
