<?php

namespace App\Http\Controllers\Admin\ServicePoint;

use App\Http\Controllers\Admin\BaseAdminController;

use App\Models\SvgMapTable;
use App\Models\ServicePoint;
use Illuminate\Validation\Rule;

class ServicePointController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 服务设施列表
	 *
	 * @author yyj 20171108
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function service_point_list()
	{
		// 处理排序
		$query = ServicePoint::orderBy('id', 'desc');
		// 筛选是名称
		if (request('service_name')) {
			$query->where('service_name', 'LIKE', "%" . request('service_name') . "%");
		}
		// 筛选地图类别
		if (request('map_id')) {
			$query->where('map_id', request('map_id'));
		}
		// 取得列表
		$info = $query->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());

		$map_info = SvgMapTable::orderBy('id', 'asc')->get();
		return view('admin.service_point.service_point_list', [
			'info' => $info,
			'map_info' => $map_info,
		]);
	}

	/**
	 * 服务设施编辑
	 *
	 * @author yyj 20171027
	 * @param  int $id 设施id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function service_point_edit($id)
	{
		if (request()->isMethod('post')) {
			$this->validate(request(), [
				'service_name' => [
					'required',
					'max:20'
				],
				'img' => 'required',
				'map_id' => 'required',
				'x' => 'required',
				'y' => 'required',
			]);

			$data = [
				'img' => request('img'),
				'map_id' => request('map_id'),
				'x' => request('x'),
				'y' => request('y'),
				'service_name' => request('service_name'),
			];
			//基本信息入库
			if ($id == 'add') {
				$this->validate(request(), [
					'service_name' => [
						Rule::unique('service_point', 'service_name'),
					],
				]);
				ServicePoint::create($data);
			} else {
				$this->validate(request(), [
					'service_name' => [
						Rule::unique('service_point', 'service_name')->ignore($id, 'id'),
					],
				]);
				ServicePoint::where('id', $id)->update($data);
			}
			return $this->success(get_session_url('service_list'));
		} else {
			$info = [];
			if ($id !== 'add') {
				$info = ServicePoint::where('id', $id)->first()->toArray();
			}
			$map_info = SvgMapTable::orderBy('id', 'asc')->get();
			return view('admin.service_point.service_point_edit', array(
				'info' => $info,
				'map_info' => $map_info,
				'id' => $id
			));
		}
	}

	/**
	 * 服务设施删除
	 *
	 * @author yyj 20171109
	 * @param  int $id 服务设施id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function service_point_delete($id)
	{
		$img_path = base_path() . '/public' . request('img_path');
		if (file_exists($img_path)) {
			unlink($img_path);
		}
		ServicePoint::where('id', $id)->delete();
		return $this->success(get_session_url('service_list'));
	}

}
