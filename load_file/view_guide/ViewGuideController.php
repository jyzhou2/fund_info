<?php

namespace App\Http\Controllers\Api;

use App\Dao\NavigationRoadDao;
use App\Dao\SettingDao;
use App\Exceptions\ApiErrorException;
use App\Models\Exhibit;
use App\Models\ExhibitLanguage;
use App\Models\ViewGuide;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ViewGuideController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 获取周围展品数据
	 *
	 * @author yyj 20180630
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /viewguide/near_exhibit 获取周围展品数据
	 * @apiGroup ViewGuide
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {int} x 当前所在位置的x坐标
	 * @apiParam {int} y 当前所在位置的y坐标
	 * @apiParam {int} map_id 地图id
	 * @apiParam {int} select_distance 查询范围，单位米
	 * @apiParam {int} select_num 查询结果最大显示个数
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {float} map_angle 手机朝向地图y轴正方向时与正北方向的夹角
	 * @apiSuccess {array} exhibit_list 展品列表
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {int} exhibit_name 展品名称
	 * @apiSuccess {float} distance 距离（米）
	 * @apiSuccess {string} exhibit_img 展品图片
	 * @apiSuccess {float} x x坐标
	 * @apiSuccess {float} y y坐标
	 */
	public function near_exhibit()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'x' => 'required|min:0|numeric',
			'y' => 'required|min:0|numeric',
			'map_id' => 'required|min:1|integer',
			'select_distance' => 'required|min:1|integer',
			'select_num' => 'required|min:1|integer'
		]);
		$language = request('language');
		$x = request('x');
		$y = request('y');
		$map_id = request('map_id');
		$select_distance = request('select_distance');
		$select_num = request('select_num');
		$map_info = NavigationRoadDao::get_map_cache_map_size($map_id, 1);
		$distance_px = $select_distance * $map_info['map_size'];
		//获取展品图片及距离信息
		$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->whereBetween('x', [
			$x - $distance_px,
			$x + $distance_px
		])->whereBetween('y', [
			$y - $distance_px,
			$y + $distance_px
		])->where('exhibit.map_id', $map_id)->where('exhibit_language.language', $language)->select('exhibit.id as exhibit_id', 'exhibit.exhibit_img', 'exhibit.x', 'exhibit.y', 'exhibit_language.exhibit_name')->get()->toArray();
		$arr = [];
		foreach ($exhibit_list as $k => $g) {
			$distance = sqrt(pow(abs($g['x'] - $x), 2) + pow(abs($g['y'] - $y), 2));
			$exhibit_list[$k]['distance'] = round($distance / $map_info['map_size'], 2);
			$exhibit_list[$k]['exhibit_img'] = get_file_url(json_decode($g['exhibit_img'], true)['exhibit_list']);
		}
		$exhibit_list = sortArr($exhibit_list, 'distance', SORT_ASC);
		return response_json(1, [
			'map_angle' => $map_info['map_angle'],
			'exhibit_list' => array_slice($exhibit_list, 0, $select_num)
		]);
	}

	/**
	 * 获取展品详情
	 *
	 * @author yyj 20180702
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /viewguide/exhibit_info 获取展品详情
	 * @apiGroup ViewGuide
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {int} exhibit_id 展品id
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {string} exhibit_img 展品图片
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} audio 讲解语音url
	 * @apiSuccess {string} content_url 内容url
	 * */
	public function exhibit_info()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'exhibit_id' => 'required|min:1|int',
		]);
		$p = request('p', 'a');
		$language = request('language');
		$exhibit_id = request('exhibit_id');

		$img = Exhibit::where('id', $exhibit_id)->value('exhibit_img');
		$data['exhibit_id'] = $exhibit_id;
		$data['exhibit_img'] = get_file_url(json_decode($img, true)['exhibit_list']);
		$info = ExhibitLanguage::where('language', $language)->where('exhibit_id', $exhibit_id)->select('exhibit_name', 'audio')->first();
		$data['exhibit_name'] = $info->exhibit_name;
		$data['audio'] = get_file_url($info->audio);
		$data['content_url'] = get_file_url('/api/viewguide/exhibit_content_info/' . $language . '/' . $exhibit_id . '?p=' . $p . '&language=' . $language);
		return response_json(1, $data);
	}

	/**
	 * 展品详情页
	 */
	public function exhibit_content_info($language, $exhibit_id)
	{
		$info = ExhibitLanguage::where('language', $language)->where('exhibit_id', $exhibit_id)->select('content', 'exhibit_name')->first();
		return view('api.view_guide.exhibit_content_info', array(
			'info' => $info,
			'language' => $language,
		));
	}

	/**
	 * 获取到达展品的最短路线
	 *
	 * @author yyj 20180702
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /viewguide/shortest_route 获取到达展品的最短路线
	 * @apiGroup ViewGuide
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {int} x 当前所在位置的x坐标
	 * @apiParam {int} y 当前所在位置的y坐标
	 * @apiParam {int} map_id 地图id
	 * @apiParam {int} exhibit_id 所要到达展品的id
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {array} road_info 路线坐标数组
	 * @apiSuccess {float} x x坐标
	 * @apiSuccess {float} y y坐标
	 * @apiSuccess {float} total_distance 总距离（米）
	 * */
	public function shortest_route()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'x' => 'required|min:0|numeric',
			'y' => 'required|min:0|numeric',
			'map_id' => 'required|min:1|integer',
			'exhibit_id' => 'required|min:1|integer',
		]);
		$language = request('language');
		$x = request('x');
		$y = request('y');
		$map_id = request('map_id');
		$exhibit_id = request('exhibit_id');
		//获取展品坐标
		$exhibit_info = Exhibit::where('id', $exhibit_id)->select('x', 'y', 'map_id')->first();
		if (empty($exhibit_info)) {
			return response_json(0, [], '展品不存在');
		}
		if ($exhibit_info->map_id != $map_id) {
			return response_json(0, [], '该展品不在当前地图中，无法导航');
		}
		$data = NavigationRoadDao::get_road($x, $y, $exhibit_info->x, $exhibit_info->y, $map_id, 2);
		return response_json(1, $data);
	}

	/**
	 * 获取数据库资源
	 *
	 * @author yyj 20180705
	 *
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /viewguide/get_data 获取数据库资源
	 * @apiGroup ViewGuide
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} map_id 地图id,填0返回所有数据
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} md5 资源版本MD5;不一致需要更新资源。
	 * @apiSuccess {string} resource_path 下载地址
	 * @apiSuccess {array} data 展品数据
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {int} map_id 地图id
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} img_name 图片名称
	 * */
	public function get_data()
	{
		$this->validate([
			'map_id' => 'required|min:0|integer',
		]);
		$map_id = request('map_id');
		$md5 = SettingDao::getSetting('viewguide_update_md5');
		if ($map_id) {
			$info = ViewGuide::where('map_id', $map_id)->select('exhibit_name', 'exhibit_id', 'map_id', 'zip_img')->get()->toArray();
			$path = '/uploadfiles/viewguide_resource/' . $map_id . '.zip';
			if (!file_exists(base_path() . '/public' . $path)) {
				$path = '';
			}
		} else {
			$info = ViewGuide::select('exhibit_name', 'exhibit_id', 'map_id', 'zip_img')->get()->toArray();
			$path = '/uploadfiles/viewguide_resource/resource.zip';
			if (!file_exists(base_path() . '/public' . $path)) {
				$path = '';
			}
		}
		$data = [];
		foreach ($info as $k => $g) {
			$img_arr = json_decode($g['zip_img']);
			foreach ($img_arr as $kk => $gg) {
				$data[] = [
					'exhibit_id' => $g['exhibit_id'],
					'map_id' => $g['map_id'],
					'exhibit_name' => $g['exhibit_name'],
					'img_name' => $gg,
				];
			}
		}
		$arr['md5'] = $md5;
		$arr['resource_path'] = get_file_url($path);
		$arr['data'] = $data;
		return response_json(1, $arr);
	}
}
