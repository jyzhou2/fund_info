<?php

namespace App\Http\Controllers\Admin\NavigationRoad;

use App\Dao\NavigationRoadDao;
use App\Models\NavigationPoint;
use App\Models\NavigationRoad;
use App\Models\SvgMapTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseAdminController;

class NavigationRoadController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 路线编辑
	 *
	 * @author yyj 20180630
	 */
	public function route_edit()
	{
		$map_id = request('map_id');
		$road_id = request('road_id');
		if (empty($map_id)) {
			$map = SvgMapTable::first();
			if(empty($map)){
				echo '<h1>请先上传地图</h1>';exit;
			}
			$map_id = $map->id;
		} else {
			$map = SvgMapTable::find($map_id);
		}
		$road_list = NavigationRoad::where('map_id', $map_id)->orderBy('id', 'asc')->get();
		$order_array = array();
		$point_id_list = array();
		//区的所有成对的点
		foreach ($road_list as $road) {
			$order_array [] = array(
				'start_id' => $road->start_id,
				'end_id' => $road->end_id
			);
			if (!in_array($road->start_id, $point_id_list)) {
				$point_id_list[] = $road->start_id;
			}
			if (!in_array($road->end_id, $point_id_list)) {
				$point_id_list[] = $road->end_id;
			}
		}
		//获得路线上所有的点的信息
		$raw_point_list = NavigationPoint::whereIn('auto_num', $point_id_list)->orderBy('id', 'asc')->get();
		$point_list = array();
		//数组重新组装
		foreach ($raw_point_list as $point) {
			if (!isset($point_list[$point->auto_num])) {
				$point_list[$point->auto_num] = array(
					'name' => $point->auto_num,
					'geoCoord' => array(
						$point->x,
						$point->y
					)
				);
			}
		}
		//得到一条线上的数据
		$info = array();
		foreach ($order_array as $item) {
			if (isset($point_list[$item['start_id']]) && isset($point_list[$item['end_id']])) {
				$info[] = array(
					array(
						'name' => $item['start_id'],
						'geoCoord' => array(
							$point_list[$item['start_id']]['geoCoord'][0],
							$point_list[$item['start_id']]['geoCoord'][1]
						)
					),
					array(
						'name' => $item['end_id'],
						'geoCoord' => array(
							$point_list[$item['end_id']]['geoCoord'][0],
							$point_list[$item['end_id']]['geoCoord'][1]
						)
					)
				);
			}

		}

		return view('admin.navigation_road.navigation_edit', [
			'lineArr' => empty($map->route_line_list) ? \json_encode(array()) : $map->route_line_list,
			'pointArr' => empty($map->route_point_list) ? \json_encode(array()) : $map->route_point_list,
			'map_id' => $map_id,
			'map_path' => $map->map_path,
			'road_id' => $road_id,
			'map_list' => SvgMapTable::all()
		]);
	}

	public function route_save()
	{
		$map_id = request('map_id');
		$point_data = request('point_data');
		$area_data = request('area_data');
		if (empty($point_data) || empty($area_data)) {
			return $this->error('请绘制后提交');
		}
		$point_data = json_decode($point_data, true);
		$map = SvgMapTable::find($map_id);
		if (empty($map)) {
			return $this->error('抱歉参数有误');
		}
		$map->route_point_list = json_encode(request('point_data'));
		$map->route_line_list = json_encode(request('area_data'));
		$map->save();
		//删除当前地图所有点位
		NavigationPoint::where('map_id', $map_id)->delete();
		$point_list = array();
		$now = time();
		foreach ($point_data as $item) {
			$x = $item['geoCoord'][0];
			$y = $item['geoCoord'][1];
			$point_list[] = array(
				'map_id' => $map_id,
				'x' => $x,
				'y' => $y,
				'auto_num' => $item['name'],
				'axis' => json_encode(array(
					'x' => $x,
					'y' => $y
				)),
				'datetime' => $now
			);
		}
		NavigationPoint::insert($point_list);
		NavigationRoad::where('map_id', $map_id)->delete();

		$area_data = json_decode($area_data, true);
		$line_list = array();
		$size = 1;
		foreach ($area_data as $item) {
			$distance = abs($item[0]['geoCoord'][0] - $item[1]['geoCoord'][0]) * abs($item[0]['geoCoord'][0] - $item[1]['geoCoord'][0]) + abs($item[0]['geoCoord'][1] - $item[1]['geoCoord'][1]) * abs($item[0]['geoCoord'][1] - $item[1]['geoCoord'][1]);
			$distance = sqrt($distance) / $size;
			$distance = round($distance, 2);
			$start_point = NavigationPoint::where('x', $item[0]['geoCoord'][0])->where('y', $item[0]['geoCoord'][1])->first();
			if (empty($start_point)) {
				$start_point = new NavigationPoint();
				$start_point->x = $item[0]['geoCoord'][0];
				$start_point->y = $item[0]['geoCoord'][1];
				$start_point->auto_num = $item[0]['name'];
				$start_point->datetime = time();
				$start_point->map_id = $map_id;
				$start_point->save();
			}
			$end_point = NavigationPoint::where('x', $item[1]['geoCoord'][0])->where('y', $item[1]['geoCoord'][1])->first();
			if (empty($end_point)) {
				$end_point = new NavigationPoint();
				$end_point->x = $item[1]['geoCoord'][0];
				$end_point->y = $item[1]['geoCoord'][1];
				$end_point->map_id = $map_id;
				$end_point->auto_num = $item[1]['name'];
				$end_point->datetime = time();
				$end_point->save();
			}
			$line_list[] = array(
				'map_id' => $map_id,
				'start_id' => $start_point->id,
				'end_id' => $end_point->id,
				'axis' => $start_point->id . "_" . $end_point->id,
				'floors' => 1,
				'distance' => $distance,
				'road' => json_encode(array(
					array(
						'x' => $item[0]['geoCoord'][0],
						'y' => $item[0]['geoCoord'][1]
					),
					array(
						'x' => $item[1]['geoCoord'][0],
						'y' => $item[1]['geoCoord'][1]
					)
				))
			);
		}
		NavigationRoad::insert($line_list);
		//更新缓存
		NavigationRoadDao::get_navigation_point_cache($map_id, 2);
		NavigationRoadDao::get_navigation_road_cache($map_id, 2);
		return $this->success(get_session_url('index'));

	}
}
