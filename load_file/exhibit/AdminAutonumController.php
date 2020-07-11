<?php

namespace App\Http\Controllers\Admin\Data;

use App\Dao\ExhibitDao;
use App\Models\Autonum;
use App\Models\Exhibit;
use App\Models\SvgMapTable;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseAdminController;
use Illuminate\Validation\Rule;

class AutonumController extends BaseAdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 蓝牙关联列表
	 *
	 * @author yyj 20171116
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function autonum_list()
	{
		// 处理排序
		$query = Autonum::orderBy('autonum', 'asc');
		// 筛选是名称
		if (request('autonum')) {
			$query->where('autonum', 'LIKE', "%" . request('autonum') . "%");
		}
		// 取得列表
		$info = $query->paginate(parent::$perpage)->appends(app('request')->all());
		return view('admin.data.autonum_list', [
			'info' => $info,
		]);
	}

	/**
	 * 编辑蓝牙关联
	 *
	 * @author yyj 20171116
	 * @param  int $id autonum_id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($id)
	{
		if (request()->isMethod('post')) {
			$validate_arr = [
				'auto_exhibit_id' => [
					'required'
				],
				'autonum' => 'required'
			];
			if (config('exhibit_config.is_set_autonum_x_y')) {
				$validate_arr['map_id'] = 'required';
				$validate_arr['x'] = 'required';
				$validate_arr['y'] = 'required';
			}
			$this->validate(request(), $validate_arr);
			$exhibit_name_list = request('exhibiti_name');
			$auto_exhibit_id = request('auto_exhibit_id');
			$exhibit_name = [];
			foreach ($auto_exhibit_id as $k => $g) {
				$exhibit_name[] = $exhibit_name_list[$g];
			}
			$map_id_num = Exhibit::whereIn('id', $auto_exhibit_id)->groupBy('map_id')->select('map_id')->get()->toArray();
			$map_id = $map_id_num[0]['map_id'];
			if (count($map_id_num) > 1) {
				return $this->error('只能关联同一张地图下的展品');
			}
			if (config('exhibit_config.is_set_autonum_x_y')) {
				$validate_arr['map_id'] = 'required';
				$avg_x = request('x');
				$avg_y = request('y');
				if ($map_id != request('map_id')) {
					return $this->error('标注的点位与展品不在同一张地图上');
				}
			} else {
				$avg_x = Exhibit::whereIn('id', $auto_exhibit_id)->avg('x');
				$avg_y = Exhibit::whereIn('id', $auto_exhibit_id)->avg('y');
			}
			//判断展品是否重复关联
			$exhibit_more_autonum=config('exhibit_config.exhibit_more_autonum');
			if($exhibit_more_autonum===false){
				$auto_exhibit_id_arr=Autonum::where('map_id', $map_id)->where('autonum','<>',request('autonum'))->pluck('exhibit_list')->toArray();
				foreach ($auto_exhibit_id_arr as $g){
					$intersect_arr=array_intersect($auto_exhibit_id,json_decode($g,true));
					if(count($intersect_arr)>0){
						$exhibit_name_arr=Exhibit::whereIn('id', $intersect_arr)->pluck('exhibit_name')->toArray();
						$name_string=implode(',',$exhibit_name_arr);
						return $this->error($name_string.'不能重复关联');
					}
				}
			}
			$data = [
				'autonum' => request('autonum'),
				'exhibit_list' => json_encode($auto_exhibit_id),
				'exhibit_name' => json_encode($exhibit_name),
				'x' => round($avg_x, 0),
				'y' => round($avg_y, 0),
				'map_id' => $map_id,
				'mx_and' => request('mx_and'),
				'mx_dlj' => request('mx_dlj'),
				'mx_ios' => request('mx_ios'),
			];
			//基本信息入库
			if ($id == 'add') {
				$this->validate(request(), [
					'autonum' => [
						Rule::unique('autonum_list', 'autonum'),
					],
				]);
				Autonum::create($data);
			} else {
				$this->validate(request(), [
					'autonum' => [
						Rule::unique('autonum_list', 'autonum')->ignore($id, 'id'),
					],
				]);
				Autonum::where('id', $id)->update($data);
			}
			return $this->success(get_session_url('autonum_list'));
		} else {
			$info = [];
			$road_list = [];
			if ($id !== 'add') {
				$info = Autonum::where('id', $id)->first()->toArray();
				$road_list = json_decode($info['exhibit_list'], true);
			}
			$re_data = ExhibitDao::autonum_list($road_list);
			$exhibit_list = $re_data['data'];
			$map_info = SvgMapTable::orderBy('id', 'asc')->get();
			return view('admin.data.autonum_edit', array(
				'info' => $info,
				'id' => $id,
				'exhibit_list' => $exhibit_list,
				'is_add' => $re_data['is_add'],
				'map_info' => $map_info
			));
		}
	}

	/**
	 * 蓝牙关联删除
	 *
	 * @author yyj 20171116
	 * @param  int $id id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function delete($id)
	{
		Autonum::where('id', $id)->delete();
		return $this->success(get_session_url('autonum_list'));
	}
}
