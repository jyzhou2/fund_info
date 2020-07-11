<?php

namespace App\Http\Controllers\Api;

use App\Dao\ResourceDao;
use App\Exceptions\ApiErrorException;
use App\Models\Autonum;
use App\Models\Exhibition;
use App\Models\Exhibit;
use App\Models\SvgMapTable;
use App\Models\VersionList;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 展品导览相关接口
 *
 * @author yyj 20171112
 * @package App\Http\Controllers\Api
 */
class MapExhibitController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 获取地图页展品数据
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /map_exhibit 01.获取地图页展品数据
	 * @apiGroup MapExhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {int} map_id 地图编号,传0返回所有数据
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {string} exhibit_icon1 地图页图片(亮)
	 * @apiSuccess {string} exhibit_icon2 地图页图片(暗)
	 * @apiSuccess {int} map_id 地图编号
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 * @apiSuccess {array} auto_list 蓝牙关联列表
	 * @apiSuccess {string} mx_and 安卓门限
	 * @apiSuccess {string} mx_ios ios门限
	 * @apiSuccess {int} autonum 蓝牙编号
	 * @apiSuccess {string} auto_string 关联的蓝牙号
	 *
	 */
	public function map_exhibit()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'map_id' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$map_id = request('map_id', 0);
		$data = [];
		//获取展品信息
		$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->orderBy('exhibit.order_id','ASC')->where('exhibit_language.language', $language)->where('exhibit.is_show_map', 1)->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit.map_id', 'exhibit.x', 'exhibit.y', 'exhibit_language.audio');
		//获取蓝牙关联详情
		$auto_info = Autonum::select('exhibit_list', 'autonum', 'mx_and', 'mx_ios');
		if ($map_id) {
			$exhibit_list = $exhibit_list->where('exhibit.map_id', $map_id);
			$auto_info = $auto_info->where('map_id', $map_id);
		}
		$exhibit_list = $exhibit_list->get();
		$auto_info = $auto_info->get()->toArray();

		foreach ($auto_info as $k => $g) {
			$auto_info[$k]['exhibit_list'] = json_decode($g['exhibit_list']);
			foreach ($auto_info[$k]['exhibit_list'] as $kk => $gg) {
				$auto_string_list[$gg][] = $g['autonum'];
				$auto_list[$gg][] = [
					'autonum' => $g['autonum'],
					'mx_and' => $g['mx_and'],
					'mx_ios' => $g['mx_ios'],
				];
			}
		}
		foreach ($exhibit_list as $k => $g) {
			$data[$k]['exhibit_id'] = $g->exhibit_id;
			$data[$k]['exhibit_name'] = $g->exhibit_name;
			$data[$k]['exhibit_icon1'] = get_file_url(json_decode($g->exhibit_img, true)['exhibit_icon1']);
			$data[$k]['exhibit_icon2'] = get_file_url(json_decode($g->exhibit_img, true)['exhibit_icon2']);
			$data[$k]['map_id'] = $g->map_id;
			$data[$k]['x'] = $g->x;
			$data[$k]['y'] = $g->y;
			if (isset($auto_string_list[$g->exhibit_id]) && isset($auto_list[$g->exhibit_id])) {
				$data[$k]['auto_string'] = implode('#', $auto_string_list[$g->exhibit_id]);
				$data[$k]['auto_list'] = $auto_list[$g->exhibit_id];
			} else {
				$data[$k]['auto_string'] = '0';
				$data[$k]['auto_list'] = [];
			}
		}

		return response_json(1, $data);
	}

	/**
	 * 获取附近展厅
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /map_near_exhibition 02.获取附近展厅
	 * @apiGroup MapExhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {string} autonum 收到的蓝牙号
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {int} exhibition_id 展厅id
	 * @apiSuccess {string} exhibition_img 展厅图片
	 *
	 */
	public function map_near_exhibition()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'autonum' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$autonum = request('autonum', 1);
		$exhibit = Autonum::where('autonum', $autonum)->value('exhibit_list');
		$exhibit_arr = json_decode($exhibit, true);
		if (!empty($exhibit_arr) && is_array($exhibit_arr)) {
			$exhibition_id = Exhibit::whereIn('id', $exhibit_arr)->pluck('exhibition_id')->toArray();
			if (!empty($exhibition_id) && is_array($exhibition_id)) {
				$exhibition = Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition_language.language', $language)->whereIn('exhibition.id', $exhibition_id)->select('exhibition_language.exhibition_name', 'exhibition.exhibition_img', 'exhibition.id as exhibition_id')->get()->toArray();
				foreach ($exhibition as $k => $g) {
					$exhibition[$k]['exhibition_img'] = json_decode($g['exhibition_img'], true)['list_img'];
				}
				return response_json(1, $exhibition);
			} else {
				return response_json(1, []);
			}
		} else {
			return response_json(1, []);
		}
	}

	/**
	 * 获取附近展品
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /map_near_exhibit 03.获取附近展品
	 * @apiGroup MapExhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {string} autonum_str 收到的蓝牙号用#拼接例如101#102
	 * @apiParam {string} exhibit_id 要过滤的展品编号,没有要过滤的就传0
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} exhibit_id 展品编号
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} floor 所在楼层
	 * @apiSuccess {string} map_id 地图id
	 * @apiSuccess {string} exhibition_id 展品id
	 */
	public function map_near_exhibit()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'autonum_str' => 'required|max:50|string',
			'exhibit_id' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$autonum_str = request('autonum_str', 0);
		$exhibit_id = request('exhibit_id', 0);
		$arr = explode('#', $autonum_str);
		$exhibit_id_arr = Autonum::whereIn('autonum', $arr)->pluck('exhibit_list');
		if (empty($exhibit_id_arr)) {
			return response_json(1, []);
		} else {
			$exhibit_arr = [];
			foreach ($exhibit_id_arr as $k => $g) {
				$exhibit_arr = array_merge(json_decode($g, true), $exhibit_arr);
			}
			$exhibit_arr = array_unique($exhibit_arr);
			if (!empty($exhibit_id)) {
				$exhibit_arr = array_diff($exhibit_arr, [$exhibit_id]);
			}
		}
		$exhibit_list = Exhibit::join('exhibit_language', function ($join) use ($language) {
			$join->on('exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', '=', $language);
		})->join('exhibition', 'exhibition.id', '=', 'exhibit.exhibition_id')->join('exhibition_language', function ($join) use ($language) {
			$join->on('exhibition.id', '=', 'exhibition_language.exhibition_id')->where('exhibition_language.language', '=', $language);
		})->orderBy('exhibit.order_id','ASC')->where('exhibit.is_show_list', 1)->whereIn('exhibit.id', $exhibit_arr)->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibition_language.exhibition_name', 'exhibition.floor_id', 'exhibit.map_id', 'exhibit.exhibition_id')->get();

		$data = [];
		foreach ($exhibit_list as $k => $g) {
			$imgs = json_decode($g['exhibit_img'], true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$data[$k]['exhibit_name'] = $g['exhibit_name'];
			$data[$k]['exhibit_id'] = $g['exhibit_id'];
			$data[$k]['exhibit_list_img'] = get_file_url($imgs);
			$data[$k]['exhibition_name'] = $g['exhibition_name'];
			$data[$k]['floor'] = config('floor')[$g['floor_id']];
			$data[$k]['map_id'] = $g['map_id'];
			$data[$k]['exhibition_id'] = $g['exhibition_id'];
		}
		return response_json(1, $data);
	}

	/**
	 * 资源版本更新(导览机专用)
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /update_version_resource 资源版本更新(导览机专用)
	 * @apiGroup Resource
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} version_id 资源版本编号，导览机内置版本号为0
	 * @apiSuccess {string} is_update 是否需要更新1需要，0不需要
	 * @apiSuccess {string} version_id 版本编号
	 * @apiSuccess {string} down_url 资源下载地址
	 */
	public function update_version_resource()
	{
		$this->validate([
			'version_id' => 'required|min:0|integer',
		]);
		$version = request('version_id');
		//白板程序直接下载整包资源，版本更新到最高。
		//当前最高版本
		$the_newest = VersionList::where('type', '<>', 0)->OrderBy('id', 'desc')->value('id');

		if ($version == $the_newest) {
			$info['is_update'] = 0;
			$info['version_id'] = $the_newest;
			$info['down_url'] = '';
		} elseif ($version == 0) {
			$info['is_update'] = 1;
			$info['version_id'] = $the_newest;
			$info['down_url'] = get_file_url('/resource_zip/resource.zip');
		} elseif ($version < $the_newest) {
			$info = $this->get_zip($version, $the_newest);
		} else {
			return response_json(0, [], '版本号错误');
		}
		return response_json(1, $info, '查询成功');
	}

	private function get_zip($version, $the_newest)
	{
		//获取下一版本编号
		$next_version = $version + 1;
		if (file_exists(base_path() . '/public/resource_zip/version_' . $next_version . '/resource.zip')) {
			$info['is_update'] = 1;
			$info['version_id'] = $next_version;
			$info['down_url'] = get_file_url('/resource_zip/version_' . $next_version . '/resource.zip');
		} else {
			if ($next_version < $the_newest) {
				//直接全量更新
				$info['is_update'] = 1;
				$info['version_id'] = $the_newest;
				$info['down_url'] = get_file_url('/resource_zip/resource.zip');
			} else {
				$info['is_update'] = 0;
				$info['version_id'] = $the_newest;
				$info['down_url'] = '';
			}
		}
		return $info;
	}

	/**
	 * 获取所有数据库
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /datas_info 获取所有数据库,获取后直接覆盖原数据库(导览机专用)
	 * @apiGroup Resource
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiSuccess {array} autonum_list 多模蓝牙数据列表
	 * @apiSuccess {int} autonum 多模蓝牙编号
	 * @apiSuccess {int} map_id 展厅编号
	 * @apiSuccess {int} x x坐标
	 * @apiSuccess {int} y y坐标
	 * @apiSuccess {int} mx_dlj 触发门限
	 * @apiSuccess {array} map_list 地图数据
	 * @apiSuccess {int} map_id 地图编号
	 * @apiSuccess {int} width 宽
	 * @apiSuccess {int} height 高
	 * @apiSuccess {int} floor_id 楼层id
	 * @apiSuccess {array} exhibition_* 展厅语种数据
	 * @apiSuccess {int} exhibition_id 展厅编号
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} exhibition_address 展厅地址
	 * @apiSuccess {int} is_lb 是否轮播1轮播，2不轮播
	 * @apiSuccess {int} type 展厅类别1常设展厅2临时展厅
	 * @apiSuccess {int} is_show_list 是否显示1显示2不显示
	 * @apiSuccess {int} order_id 排序编号，越小的在越前面
	 * @apiSuccess {int} floor_id 楼层id
	 * @apiSuccess {array} exhibit_* 展品语种数据
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {int} is_lb 是否轮播
	 * @apiSuccess {int} is_show_map 是否在地图页显示
	 * @apiSuccess {int} is_show_list 是否在列表页显示
	 * @apiSuccess {int} map_id 所属地图id
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 * @apiSuccess {string} exhibition_id 所属展厅id
	 * @apiSuccess {string} exhibit_num 展品编号
	 * @apiSuccess {int} order_id 排序编号，越小的在越前面
	 * @apiSuccess {int} type 展品类别1常展2临展
	 * @apiSuccess {string} autonum_list 相关联的蓝牙号
	 * @apiSuccess {int} imgs_num 展品图片数量
	 */
	public function datas_info()
	{
		//获取多模蓝牙数据
		$info['autonum_list'] = Autonum::orderBy('autonum', 'asc')->select('autonum', 'map_id', 'x', 'y', 'mx_dlj')->get()->toArray();
		//获取地图数据
		$info['map_list'] = SvgMapTable::select('id as map_id', 'floor_id', 'width', 'height')->get()->toArray();
		//获取语种数据
		foreach (config('language') as $k => $g) {
			//展厅数据
			$info['exhibition_' . $g['dir']] = Exhibition::join('exhibition_language', 'exhibition.id', '=', 'exhibition_language.exhibition_id')->where('exhibition_language.language', '=', $k)->select('exhibition_language.exhibition_id', 'exhibition_language.exhibition_name', 'exhibition_language.exhibition_address', 'exhibition.is_lb', 'exhibition.type', 'exhibition.is_show_list', 'exhibition.order_id', 'exhibition.floor_id')->get()->toArray();
		}
		//获取蓝牙关联详情
		$auto_info = Autonum::select('exhibit_list', 'autonum', 'mx_and', 'mx_ios');
		$auto_info = $auto_info->get()->toArray();
		foreach ($auto_info as $k => $g) {
			$auto_info[$k]['exhibit_list'] = json_decode($g['exhibit_list']);
			foreach ($auto_info[$k]['exhibit_list'] as $kk => $gg) {
				$auto_string_list[$gg][] = $g['autonum'];
			}
		}
		foreach (config('language') as $k => $g) {
			//展品数据
			$info['exhibit_' . $g['dir']] = Exhibit::join('exhibit_language', 'exhibit.id', '=', 'exhibit_language.exhibit_id')->where('exhibit_language.language', '=', $k)->select('exhibit_language.exhibit_id', 'exhibit_language.exhibit_name', 'exhibit.is_lb', 'exhibit.is_show_map', 'exhibit.is_show_list', 'exhibit.map_id', 'exhibit.x', 'exhibit.y', 'exhibit.exhibition_id', 'exhibit.exhibit_num', 'exhibit.order_id', 'exhibit.type', 'exhibit.imgs_num')->get()->toArray();
			foreach ($info['exhibit_' . $g['dir']] as $kk => $gg) {
				if (isset($auto_string_list[$gg['exhibit_id']])) {
					$info['exhibit_' . $g['dir']][$kk]['autonum_list'] = implode('#', $auto_string_list[$gg['exhibit_id']]);
				} else {
					$info['exhibit_' . $g['dir']][$kk]['autonum_list'] = '';
				}
			}
		}
		return response_json(1, $info, '查询成功');
	}
}