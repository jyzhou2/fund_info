<?php

namespace App\Dao;

use App\Models\SvgMapTable;
use App\Models\NavigationRoad;
use App\Models\NavigationPoint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

/**
 * 展品列表距离排序
 *
 * @author yyj 20171111
 */
class NavigationRoadDao extends NavigationRoad
{

	/**
	 * 路径导航计算
	 *
	 * @author yyj 20171118
	 * @param int $start_x 起点x坐标
	 * @param int $start_y 起点y坐标
	 * @param int $end_x 终点x坐标
	 * @param int $end_y 终点y坐标
	 * @param int $map_id 地图编号
	 * @param int $type 返回路线类型 1.不返回起始点坐标，2返回起始点坐标
	 * @return array
	 */
	public static function get_road($start_x, $start_y, $end_x, $end_y, $map_id, $type)
	{
		//获取导航辅助点数据
		$NavigationPoint_list = self::get_navigation_point_cache($map_id, 1);
		//获取离起点和终点最近的点
		$near_start_id = 0;
		$near_end_id = 0;
		$min_start_distance = 99999999999;
		$min_end_distance = 99999999999;
		foreach ($NavigationPoint_list as $k => $g) {
			$start_distance = sqrt(pow(abs($g['x'] - $start_x), 2) + pow(abs($g['y'] - $start_y), 2));
			$end_distance = sqrt(pow(abs($g['x'] - $end_x), 2) + pow(abs($g['y'] - $end_y), 2));
			if ($start_distance < $min_start_distance) {
				$min_start_distance = $start_distance;
				$near_start_id = $g['id'];
			}
			if ($end_distance < $min_end_distance) {
				$min_end_distance = $end_distance;
				$near_end_id = $g['id'];
			}
		}

		if (empty($near_start_id) || empty($near_end_id)) {
			return [];
		}
		//起点终点相距较近直接相连
		if ($near_start_id == $near_end_id) {
			$road[0]['x'] = $start_x;
			$road[0]['y'] = $start_y;
			$road[1]['x'] = $end_x;
			$road[1]['y'] = $end_y;
			$total_distance = sqrt(pow(abs($start_x - $end_x), 2) + pow(abs($start_y - $end_y), 2));
		} else {
			//迪杰斯特拉计算最短路径
			$road_arr = self::djstl($near_start_id, $near_end_id, $map_id);
			$road = $road_arr['road_info'];
			$total_distance = $road_arr['total_distance'];
			if ($type == 2) {
				$road_num = count($road);
				if ($road_num > 2) {
					//路径优化
					//前两个导航点坐标
					$x_start1 = $road[0]['x'];
					$y_start1 = $road[0]['y'];
					$x_start2 = $road[1]['x'];
					$y_start2 = $road[1]['y'];
					//最后两个导航点坐标
					$x_end1 = end($road)['x'];
					$y_end1 = end($road)['y'];
					$x_end2 = array_slice($road, -2, 1)[0]['x'];
					$y_end2 = array_slice($road, -2, 1)[0]['y'];
					//起点不是第一个导航点，优化起点
					if ($start_x != $x_start1 && $start_y != $y_start1) {
						$star_arr[0]['x'] = $start_x;
						$star_arr[0]['y'] = $start_y;
						if ($y_start1 == $y_start2) {
							$star_arr[1]['x'] = $start_x;
							$star_arr[1]['y'] = $y_start1;
							if ($x_start1 < $x_start2) {
								if ($start_x > $x_start1) {
									unset($road[0]);
								}

							} elseif ($x_start1 > $x_start2) {
								if ($start_x < $x_start1) {
									unset($road[0]);
								}
							}
						} elseif ($x_start1 == $x_start2) {
							$star_arr[1]['x'] = $x_start1;
							$star_arr[1]['y'] = $start_y;
							if ($y_start1 < $y_start2) {
								if ($start_y > $y_start1) {
									unset($road[0]);
								}
							} elseif ($y_start1 > $y_start2) {
								if ($start_y < $y_start1) {
									unset($road[0]);
								}
							}
						}
						//将优化后的起点数组加在road数组之前
						$road = array_merge($star_arr, $road);
					}
					//判断终点
					//终点不是最后一个导航点，优化终点
					if ($end_x != $x_end1 && $end_y != $y_end1) {
						$arr_num = count($road);
						if ($y_end1 == $y_end2) {
							$end_arr[0]['x'] = $end_x;
							$end_arr[0]['y'] = $y_end1;
							if ($x_end1 < $x_end2) {
								if ($end_x > $x_end1) {
									unset($road[$arr_num - 1]);
								}

							} elseif ($x_end1 > $x_end2) {
								if ($end_x < $x_end1) {
									unset($road[$arr_num - 1]);
								}
							}
						} elseif ($x_end1 == $x_end2) {
							$end_arr[0]['x'] = $x_end1;
							$end_arr[0]['y'] = $end_y;
							if ($y_end1 < $y_end2) {
								if ($end_y > $y_end1) {
									unset($road[$arr_num - 1]);
								}
							} elseif ($y_end1 > $y_end2) {
								if ($end_y < $y_end1) {
									unset($road[$arr_num - 1]);
								}
							}
						}
						$end_arr[1]['x'] = $end_x;
						$end_arr[1]['y'] = $end_y;
						//将优化后的起点数组加在road数组之前
						$road = array_merge($road, $end_arr);
					}
				}
			}
			//			return $road;
		}
		$map_info = self::get_map_cache_map_size($map_id, 1);
		return [
			'road_info' => $road,
			'total_distance' => round($total_distance / $map_info['map_size'], 2)
		];
	}

	/**
	 * 迪杰斯特拉 单源最短路劲计算
	 *
	 * @param int $start_id 开始点位
	 * @param int $end_id 结束点位
	 * @param int $map_id 地图id
	 * @return array
	 * @author yyj 20160830
	 */
	public static function djstl($start_id, $end_id, $map_id)
	{
		$start_node_id = $start_id;//设置开始节点
		//获取所有节点信息
		$info = self::get_navigation_point_cache($map_id, 1);
		//$info=NavigationPoint::where('map_id',$map_id)->pluck('axis', 'id')->toArray();
		$road_info = self::get_navigation_road_cache($map_id, 1);
		$i = 0;
		foreach ($info as $k => $g) {
			$node_arr[$i]['node_id'] = $k;
			$node_arr[$i]['next_node'] = array();
			$node_arr[$i]['distance'] = array();
			//获取当前点位的关联点位

			/*$road_info=NavigationRoad::where('start_id',$k)->orwhere('end_id',$k)->select('start_id','end_id','distance')->get()->toArray();*/
			foreach ($road_info as $v) {
				if ($v['start_id'] == $k || $v['end_id'] == $k) {
					$next_node = ($k == $v['start_id']) ? $v['end_id'] : $v['start_id'];
					$distance = $v['distance'];
					array_push($node_arr[$i]['next_node'], $next_node);
					array_push($node_arr[$i]['distance'], $distance);
				}
			}
			$i += 1;
		}
		//迪杰斯特拉 单源最短路劲计算
		foreach ($node_arr as $node_info) {
			foreach ($node_info['next_node'] as $key => $next_node) {
				$i_cost[$node_info['node_id']][$next_node]['distance'] = $node_info['distance'][$key];
				$i_cost[$node_info['node_id']][$next_node]['note'] = $node_info['next_node'][$key];
			}
			$i_dist[$node_info['node_id']]['distance'] = 'INF'; // 初始化为无穷大
			$i_dist[$node_info['node_id']]['road'] = $start_node_id; // 初始化为起点
			$b_mark[$node_info['node_id']] = false; // 初始化未加入
		}

		$i_dist[$start_node_id]['distance'] = 0; // 初始点到其本身的距离为0
		$b_mark[$start_node_id] = true; // 初始点加入集合
		$current_node_id = $start_node_id; // 最近加入的节点id
		$node_count = count($node_arr);//需要循环的次数

		for ($i = 0; $i < $node_count; $i++) {
			$min = 'INF';
			// 当前节点的最近距离
			if (isset($i_cost[$current_node_id])&&is_array($i_cost[$current_node_id])) {
				foreach ($i_cost[$current_node_id] as $key => $val) {
					if(isset($i_dist[$key])){
						if ($i_dist[$key]['distance'] == 'INF' || $i_dist[$key]['distance'] > $i_dist[$current_node_id]['distance'] + $val['distance']) {
							$i_dist[$key]['distance'] = $i_dist[$current_node_id]['distance'] + $val['distance'];
							$i_dist[$key]['road'] = $i_dist[$current_node_id]['road'] . '#' . $key;
						}
					}
				}
			}
			foreach ($i_dist as $key => $val) {
				if (!$b_mark[$key]) {
					if ($val['distance'] != 'INF' && ($min == 'INF' || $min > $val['distance'])) {
						$min = $val['distance'];
						$candidate_node_id = $key; // 候选最近结点id
					}
				}
			}
			if ($min == 'INF') {
				break;
			}
			$current_node_id = $candidate_node_id;
			$b_mark[$current_node_id] = true;
		}
		//获取最短路径
		$arr = $i_dist[$end_id]['road'];
		$arr = explode('#', $arr);
		/*$info_x=NavigationPoint::where('map_id',$map_id)->pluck('x', 'id')->toArray();
		$info_y=NavigationPoint::where('map_id',$map_id)->pluck('y', 'id')->toArray();*/
		$total_distance = 0;
		if (is_array($arr) && count($arr) >= 1) {
			$i = -1;
			foreach ($arr as $k => $g) {
				$road[$i + 1]['x'] = floatval($info[$g]['x']);
				$road[$i + 1]['y'] = floatval($info[$g]['y']);
				if (isset($road[$i])) {
					$distance = sqrt(pow(abs($road[$i + 1]['x'] - $road[$i]['x']), 2) + pow(abs($road[$i + 1]['y'] - $road[$i]['y']), 2));
					$total_distance = $total_distance + $distance;
				}
				$i = $i + 1;
			}

			$distance = sqrt(pow(abs($road[0]['x'] - $road[1]['x']), 2) + pow(abs($road[0]['y'] - $road[1]['y']), 2));

			$total_distance = $total_distance + $distance;

			$road[$i]['x'] = floatval($info[$end_id]['x']);
			$road[$i]['y'] = floatval($info[$end_id]['y']);
			$distance = sqrt(pow(abs($road[$i - 1]['x'] - $road[$i]['x']), 2) + pow(abs($road[$i - 1]['y'] - $road[$i]['y']), 2));
			$total_distance = $total_distance + $distance;
		} else {
			$road[0]['x'] = floatval($info[$start_id]['x']);
			$road[0]['y'] = floatval($info[$start_id]['y']);
			$road[1]['x'] = floatval($info[$end_id]['x']);
			$road[1]['y'] = floatval($info[$end_id]['y']);
			$distance = sqrt(pow(abs($road[0]['x'] - $road[1]['x']), 2) + pow(abs($road[0]['y'] - $road[1]['y']), 2));
			$total_distance = $total_distance + $distance;
		}
		return [
			'road_info' => $road,
			'total_distance' => $total_distance
		];
	}

	/**
	 * 获取地图比例尺寸
	 *
	 * @author yyj 20180524
	 * @param int $map_id map_id
	 * @param int $type 1获取缓存2更新缓存
	 * @return array
	 */
	public static function get_map_cache_map_size($map_id, $type)
	{
		$key = 'get_map_cache_map_size' . $map_id;
		if (Cache::has($key)) {
			if ($type == 1) {
				return Cache::get($key);
			} else {
				return Cache::forget($key);
			}
		} else {
			//获取地图比例尺寸
			$map_size = SvgMapTable::where('id', $map_id)->select('map_size', 'width', 'height', 'map_angle')->first();
			if (empty($map_size)) {
				Cache::forever($key, []);
			} else {
				$map_size = $map_size->toArray();
				Cache::forever($key, $map_size);
			}
			return Cache::get($key);
		}
	}

	/**
	 * 获取导航辅助点位缓存数据
	 *
	 * @author yyj 20180530
	 * @param int $map_id map_id
	 * @param int $type 1获取缓存2更新缓存
	 * @return array
	 */
	public static function get_navigation_point_cache($map_id, $type)
	{
		$key = 'get_navigation_point_cache' . $map_id;
		if (Cache::has($key)) {
			if ($type == 1) {
				return Cache::get($key);
			} else {
				return Cache::forget($key);
			}
		} else {
			//获取辅助导航点位信息
			$navigation_point_info = NavigationPoint::where('map_id', $map_id)->select('x', 'y', 'id')->get()->keyBy('id')->toArray();
			if (empty($navigation_point_info)) {
				Cache::forever($key, []);
			} else {
				Cache::forever($key, $navigation_point_info);
			}
			return Cache::get($key);
		}
	}

	/**
	 * 获取导航路线缓存数据
	 *
	 * @author yyj 20180530
	 * @param int $map_id map_id
	 * @param int $type 1获取缓存2更新缓存
	 * @return array
	 */
	public static function get_navigation_road_cache($map_id, $type)
	{
		$key = 'get_navigation_road_cache' . $map_id;
		if (Cache::has($key)) {
			if ($type == 1) {
				return Cache::get($key);
			} else {
				return Cache::forget($key);
			}
		} else {
			//获取辅助导航点位信息
			$navigation_road_info = NavigationRoad::where('map_id', $map_id)->select('start_id', 'end_id', 'distance')->get()->toArray();
			if (empty($navigation_road_info)) {
				Cache::forever($key, []);
			} else {
				Cache::forever($key, $navigation_road_info);
			}
			return Cache::get($key);
		}
	}

}
