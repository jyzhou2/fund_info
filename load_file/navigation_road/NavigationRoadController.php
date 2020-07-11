<?php

namespace App\Http\Controllers\Api;

use App\Dao\NavigationRoadDao;
use App\Exceptions\ApiErrorException;
use App\Models\SvgMapTable;
use Illuminate\Support\Facades\Auth;

class NavigationRoadController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 导航测试页
	 *  /api/navigation/dh_test?p=a
	 *
	 */
	public function dh_test()
	{
		if (request()->isMethod('post')) {
			$start_time = microtime(true);
			$map_id = request('map_id');
			$start_x = request('start_x');
			$start_y = request('start_y');
			$end_x = request('end_x');
			$end_y = request('end_y');
			$road = NavigationRoadDao::get_road($start_x, $start_y, $end_x, $end_y, $map_id, 2);
			$total_distance = $road['total_distance'];
			if (empty($road)) {
				$arr['json_info'] = [
					'code' => '001',
					'msg' => '导航路线获取失败，请检查路网是否完善',
					'info' => $road
				];
				return response_json($arr);
			}
			$road = $road['road_info'];
			$num = count($road);
			for ($i = 0; $i < $num - 1; $i++) {
				$road_info[$i][0]['x'] = $road[$i]['x'];
				$road_info[$i][0]['y'] = $road[$i]['y'];
				$road_info[$i][1]['x'] = $road[$i + 1]['x'];
				$road_info[$i][1]['y'] = $road[$i + 1]['y'];
			}
			$arr['road_info'] = $road_info;
			$arr['road_num'] = count($road_info);
			$arr['json_info'] = [
				'code' => '000',
				'msg' => '成功',
				'info' => $road,
				'total_distance' => $total_distance
			];
			$end_time = microtime(true);
			$user_time = round(($end_time - $start_time) * 1000, 2);
			$arr['user_time'] = $user_time;
			return response_json($arr);
		} else {
			$map_list = SvgMapTable::select('id', 'map_path', 'map_name')->get()->toArray();
			$res['map_list'] = $map_list;
			$map_id = request('map_id');
			if (empty($map_id)) {
				$map_id = current($map_list)['id'];
			}
			$res['map_info'] = SvgMapTable::select('id', 'map_path', 'map_name')->find($map_id)->toArray();
			return view('api.navigation_road.dh_test', $res);
		}
	}

}
