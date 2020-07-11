<?php

namespace App\Http\Controllers\Admin\Data;

use App\Dao\ExhibitDao;
use App\Dao\ResourceDao;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Exhibit;
use App\Models\Exhibition;
use App\Models\ExhibitLanguage;
use App\Models\SvgMapTable;
use App\Models\VersionList;
use App\Models\ExhibitComment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ExhibitController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 展品列表
	 *
	 * @author yyj 20171108
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibit_list()
	{
		// 处理排序
		$query = Exhibit::orderBy('order_id', 'asc')->orderBy('exhibit_num', 'asc');
		// 筛选是名称
		if (request('exhibit_name')) {
			$query->where('exhibit_name', 'LIKE', "%" . request('exhibit_name') . "%");
		}
		// 筛选是否轮播
		if (request('is_lb')) {
			$query->where('is_lb', request('is_lb'));
		}
		// 筛选地图类别
		if (request('map_id')) {
			$query->where('map_id', request('map_id'));
		}
		// 筛选展厅类别
		if (request('exhibition_id')) {
			$query->where('exhibition_id', request('exhibition_id'));
		}
		// 取得列表
		$info = $query->paginate(12);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());

		$map_info = SvgMapTable::orderBy('id', 'asc')->get();
		$exhibition_info = Exhibition::orderBy('id', 'asc')->get();
		return view('admin.data.exhibit_list', [
			'info' => $info,
			'map_info' => $map_info,
			'exhibition_info' => $exhibition_info
		]);
	}

	/**
	 * 展品编辑
	 *
	 * @author yyj 20171027
	 * @param  int $id 展品id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($id)
	{
		if (request()->isMethod('post')) {
			$imgs_arr = ResourceDao::check_upload_imgs(config('exhibit_config.exhibit.imgs'));
			if ($imgs_arr['status']) {
				$exhibit_img = json_encode($imgs_arr['imgs']);
			} else {
				return $this->error($imgs_arr['msg']);
			}
			$this->validate(request(), [
				'exhibit_num' => [
					'required',
					'max:10'
				],
				'exhibition_id' => 'required',
				'map_id' => 'required',
				'x' => 'required',
				'y' => 'required',
				'exhibit_name_1' => 'required'
			]);
			//轮播限制验证
			if (config('exhibit_config.exhibit.is_lb') && request('is_lb') == 1) {
				$lb_num = config('exhibit_config.exhibit.lb_num');
				$num = Exhibit::where('is_lb', 1)->where('id','<>',$id)->where('exhibition_id', request('exhibition_id'))->count();
				if ($num >= $lb_num) {
					return $this->error('每个展厅最多只能设置' . $lb_num . '个轮播展品');
				}
			}

			$data = [
				'exhibit_num' => request('exhibit_num'),
				'is_lb' => config('exhibit_config.exhibit.is_lb') ? request('is_lb') : 2,
				'is_show_map' => request('is_show_map'),
				'is_show_list' => request('is_show_list'),
				'exhibition_id' => request('exhibition_id'),
				'exhibit_img' => $exhibit_img,
				'map_id' => request('map_id'),
				'x' => request('x'),
				'y' => request('y'),
				'imgs_num' => is_array($imgs_arr['imgs']['exhibit_imgs']) ? count($imgs_arr['imgs']['exhibit_imgs']) : 1,
				'exhibit_name' => request('exhibit_name_1'),
			];

			//基本信息入库
			if ($id == 'add') {
				$this->validate(request(), [
					'exhibit_num' => [
						Rule::unique('exhibit', 'exhibit_num'),
					]
				]);

				$new_info = Exhibit::create($data);
				$exhibit_id = $new_info->id;
				Exhibit::where('id', $exhibit_id)->update(['order_id' => $exhibit_id]);
				$old_info = [];
			} else {
				$this->validate(request(), [
					'exhibit_num' => [
						Rule::unique('exhibit', 'exhibit_num')->ignore($id, 'id'),
					],
				]);
				if (config('exhibit_config.is_version_zip')) {
					$old_info = $this->get_old_info($id);
				}
				Exhibit::where('id', $id)->update($data);
				$exhibit_id = $id;
				ExhibitLanguage::where('exhibit_id', $exhibit_id)->delete();
			}
			$new_info = $data;
			//语种信息入库
			foreach (config('language') as $k => $g) {
				//展厅名称不为空就写入数据
				if (!empty(request('exhibit_name_' . $k))) {
					$data2 = [
						'exhibit_id' => $exhibit_id,
						'exhibit_name' => request('exhibit_name_' . $k),
						'audio' => request('exhibit_audio_' . $k),
						'language' => $k
					];
					foreach (config('exhibit_config.exhibit.content_arr') as $kkk => $ggg) {
						$data2[$ggg['key']] = request($ggg['key'] . '_' . $k);
					}
					ExhibitLanguage::create($data2);
					$new_info['language'][$k] = $data2;
				}
			}
			if (config('exhibit_config.is_version_zip')) {
				ResourceDao::update_exhibit_resource($new_info, $old_info, $exhibit_id);
			}
			return $this->success(get_session_url('exhibit_list'));
		} else {
			$info = $this->get_old_info($id);
			$map_info = SvgMapTable::orderBy('id', 'asc')->get();
			$exhibition_info = Exhibition::orderBy('id', 'asc')->get();
			return view('admin.data.exhibit_edit', array(
				'info' => $info,
				'map_info' => $map_info,
				'id' => $id,
				'exhibition_info' => $exhibition_info
			));
		}
	}

	/**
	 * 获取旧的展品信息
	 *
	 * @author yyj 20180306
	 * @param  int $id 展厅id
	 * @return array
	 */
	private function get_old_info($id)
	{
		if ($id != 'add') {
			$info = Exhibit::where('id', $id)->first()->toArray();
			$info['exhibit_img'] = empty(json_decode($info['exhibit_img'], true)) ? [] : json_decode($info['exhibit_img'], true);
			$language_info = ExhibitLanguage::where('exhibit_id', $id)->get()->toArray();
			foreach ($language_info as $k => $g) {
				$info['language'][$g['language']] = $g;
			}
		} else {
			$info = [];
		}
		return $info;
	}

	/**
	 * 展厅删除
	 *
	 * @author yyj 20171109
	 * @param  int $id 展厅id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function delete($id)
	{
		//删除图片资源
		ResourceDao::del_img('exhibit', $id);
		Exhibit::where('id', $id)->delete();
		ExhibitLanguage::where('exhibit_id', $id)->delete();
		return $this->success(get_session_url('exhibit_list'));
	}

	/**
	 * 设为轮播
	 *
	 * @author yyj 20171109
	 * @param  int $id 展厅id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function set_lb($id)
	{
		$exhibition_id = Exhibit::where('id', $id)->value('exhibition_id');
		$lb_num = config('exhibit_config.exhibit.lb_num');
		$num = Exhibit::where('is_lb', 1)->where('id','<>',$id)->where('exhibition_id', $exhibition_id)->count();
		if ($num >= $lb_num) {
			return $this->error('每个展厅最多只能设置'.$lb_num.'个轮播展品');
		}
		Exhibit::where('id', $id)->update(['is_lb' => 1]);
		return $this->success(get_session_url('exhibit_list'));
	}

	/**
	 * 取消轮播
	 *
	 * @author yyj 20171109
	 * @param  int $id 展厅id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function unset_lb($id)
	{
		Exhibit::where('id', $id)->update(['is_lb' => 2]);
		return $this->success(get_session_url('exhibit_list'));
	}

	/*
	 *
	 * 排序设置
	 * @author yyj 20180629
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 * */
	public function set_order()
	{
		if (request()->isMethod('post')) {
			$id = request('id');
			$move_type = request('move_type');
			$exhibit_id = request('exhibit_id');
			if (empty($exhibit_id)) {
				return $this->error('请选择展品');
			}
			$order_id = Exhibit::where('id', $exhibit_id)->value('order_id');
			if ($move_type == 1) {
				//获取前一个的order_id
				$next_order_info = Exhibit::where('order_id', '<', $order_id)->orderBy('order_id', 'desc')->select('order_id', 'id')->first();
				if (empty($next_order_info)) {
					$next_order_id = $order_id - 0.001;
				} else {
					if ($next_order_info->id == $id) {
						return $this->success('成功');
					} else {
						$next_order_id = $next_order_info->order_id;
					}
				}
			} else {
				//获取后一个的order_id
				$next_order_info = Exhibit::where('order_id', '>', $order_id)->orderBy('order_id', 'asc')->select('order_id', 'id')->first();
				if (empty($next_order_info)) {
					$next_order_id = $order_id + 0.001;
				} else {
					if ($next_order_info->id == $id) {
						return $this->success('成功');
					} else {
						$next_order_id = $next_order_info->order_id;
					}
				}
			}
			$new_order_id = round(($order_id + $next_order_id) / 2, 6);
			Exhibit::where('id', $id)->update(['order_id' => $new_order_id]);
			return $this->success('成功');
		} else {
			$id = request('id');
			$exhibit_name = Exhibit::where('id', $id)->value('exhibit_name');
			$list_info = Exhibit::where('id', '<>', $id)->select('exhibit_name', 'id')->get()->toArray();
			return view('admin.data.exhibit_set_order', array(
				'id' => $id,
				'exhibit_name' => $exhibit_name,
				'list_info' => $list_info,
			));
		}
	}

	/**
	 * 资源更新打包
	 *
	 * @author yyj 20180316
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function resource_zip()
	{
		if (request()->isMethod('post')) {
			$arr = request('arr');
			$type = request('type');
			$arr = self::get_resource_info($arr, $type);
			$arr['mp3_size_count'] = sizecount($arr['mp3_size']);
			$arr['image_size_count'] = sizecount($arr['image_size']);
			$arr['html_size_count'] = sizecount($arr['html_size']);
			$arr['other_size_count'] = sizecount($arr['other_size']);
			$arr['total_size_count'] = sizecount($arr['total_size']);
			if (count($arr['path']) == 0) {
				$arr['type'] = 'end';
			} else {
				$arr['type'] = 'next';
			}
			if (count($arr['folder_path']) >= 100) {
				$arr['folder_path_num'] = 1;
			}
			if (count($arr['file_path']) >= 100) {
				$arr['file_path_num'] = 1;
			}
			if (count($arr['large_file_path']) >= 10) {
				$arr['large_file_path_num'] = 1;
			}
			return response()->json($arr);
		} else {
			$version_id = ResourceDao::get_version_id();
			$new_path = base_path() . '/public/resource_zip/version_' . $version_id;
			$total_path = base_path() . '/public/resource_zip';
			if (file_exists($new_path)) {
				$is_need_update = true;
			} else {
				$is_need_update = false;
			}
			return view('admin.data.resource_zip', array(
				'new_path' => str_replace("\\", '/', $new_path),
				'total_path' => str_replace("\\", '/', $total_path),
				'is_need_update' => $is_need_update
			));
		}
	}

	/**
	 * ajax分段资源打包更新步骤1
	 * 获取资源版本更新的数据
	 *
	 * @author yyj 20170725
	 * @param array $arr 数据详情
	 * @param int $type 计算类别
	 * @return array $arr 数据详情
	 */
	private function get_resource_info($arr, $type)
	{
		$zip_exclude = config('exhibit_config.zip_exclude');
		$path = $arr['path'][0];
		unset($arr['path'][0]);
		$dh = opendir($path);
		while ($file = readdir($dh)) {
			$fullpath = $path . "/" . $file;
			//过滤需要排除的文件，过滤文件名含有中文的文件
			if ($file != "." && $file != ".." && strstr($file, 'zip') === false && !in_array($fullpath, $zip_exclude) && !preg_match('/[^\x00-\x80]/', $fullpath)) {
				if (!is_dir($fullpath)) {
					$file_info = pathinfo($fullpath);
					$file_type = $file_info['extension'];
					if ($file_type == 'mp3') {
						$arr['mp3_num'] = $arr['mp3_num'] + 1;
						$arr['mp3_size'] = $arr['mp3_size'] + abs(filesize($fullpath));
					} elseif ($file_type == 'png' || $file_type == 'jpg') {
						$arr['image_num'] = $arr['image_num'] + 1;
						$arr['image_size'] = $arr['image_size'] + abs(filesize($fullpath));
					} elseif ($file_type == 'html') {
						$arr['html_num'] = $arr['html_num'] + 1;
						$arr['html_size'] = $arr['html_size'] + abs(filesize($fullpath));
					} else {
						$arr['other_num'] = $arr['other_num'] + 1;
						$arr['other_size'] = $arr['other_size'] + abs(filesize($fullpath));
					}
					$arr['total_size'] = $arr['total_size'] + abs(filesize($fullpath));
					if (filesize($fullpath) > 2 * 1024 * 1024) {
						array_push($arr['large_file_path'], $fullpath);
					} else {
						array_push($arr['file_path'], $fullpath);
					}
				} else {
					$arr['folder_num'] = $arr['folder_num'] + 1;
					if ($type == 1 && strstr($fullpath, 'version_') === false || $type != 1) {
						array_push($arr['path'], $fullpath);
						array_push($arr['folder_path'], $fullpath);
					}
				}
				$arr['total_num'] = $arr['total_num'] + 1;
			}
		}
		closedir($dh);
		rsort($arr['path']);
		return $arr;
	}

	/**
	 * ajax分段资源打包更新步骤2
	 * 将文件夹里的非文件夹添加到压缩包中
	 *
	 * @author yyj 20170725
	 */
	public function update_zip()
	{
		if (request()->isMethod('post')) {
			$path = request('path');
			$kind = request('kind');
			if ($kind == 2) {
				$local_path = str_replace("\\", '/', base_path() . '/public/resource_zip/');
				$zip_path = base_path() . "/public/resource_zip/tm_resource.zip";
			} else {
				$version_id = ResourceDao::get_version_id();
				$local_path = str_replace("\\", '/', base_path() . "/public/resource_zip/version_" . $version_id . "/");
				$zip_path = base_path() . "/public/resource_zip/version_" . $version_id . "/resource.zip";
			}
			$zip = new \ZipArchive();
			if ($zip->open($zip_path, \ZipArchive::CREATE) === TRUE) {
				$arr['update_num'] = 0;
				foreach ($path as $g) {
					$fullpath = $g;
					if ($fullpath . '/' !== $local_path) {
						if (!is_dir($fullpath)) {
							$zip->addFile($fullpath, str_replace($local_path, '', $fullpath));
						} else {
							//创建空文件夹,必须创建，自动生成的文件夹缺少时间属性手机无法解压
							$zip->addEmptyDir(str_replace($local_path, '', $fullpath));
						}
						$arr['update_num'] = $arr['update_num'] + 1;
					}
				}
				$zip->close(); //关闭处理的zip文件
			}
			return response()->json($arr);
		}
	}

	/**
	 * ajax分段资源打包更新步骤3
	 * 判断资源是否打包成功
	 *
	 * @author yyj 20170523
	 */
	public function end_zip()
	{
		if (request()->isMethod('post')) {
			$version_id = ResourceDao::get_version_id();
			$tm_total_path = base_path() . "/public/resource_zip/tm_resource.zip";
			$total_path = base_path() . "/public/resource_zip/resource.zip";
			$update_path = base_path() . "/public/resource_zip/version_" . $version_id . "/resource.zip";
			if (!file_exists($update_path)) {
				$this->error('增量更新文件打包失败，请重试');
			}
			if (!file_exists($tm_total_path)) {
				$this->error('完整资源打包失败，请重试');
			}
			rename($tm_total_path, $total_path);
			if (!file_exists($total_path)) {
				$this->error('完整资源移动失败，请重试');
			}
			VersionList::where('type', 0)->update(['type' => 1]);
			$r = VersionList::create(['type' => 0]);
			if ($r) {
				return $this->success('资源打包成功');
			} else {
				return $this->error('操作失败，请重试');
			}
		}
	}

	/**
	 * 资源下载
	 *
	 * @author yyj 20170523
	 */
	public function down_file()
	{
		$path = base_path() . '/public/resource_zip/resource.zip';
		$title = '资源集合';
		header("Content-type:text/html;charset=utf-8");
		//首先要判断给定的文件存在与否
		if (!file_exists($path)) {
			$this->error('文件不存在');
		}
		$fp = fopen($path, "r");
		$file_size = filesize($path);
		//下载文件需要用到的头
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length:$file_size");
		Header("Content-Disposition: attachment; filename=$title.zip");
		//设置大小输出
		$buffer = 1024;
		//为了下载安全，我们最好做一个文件字节读取计数器
		$file_count = 0;
		//判断文件指针是否到了文件结束的位置(读取文件是否结束)
		while (!feof($fp) && ($file_size - $file_count) > 0) {
			$file_data = fread($fp, $buffer);
			//统计读取多少个字节数
			$file_count += $buffer;
			//把部分数据返回给浏览器
			echo $file_data;
		}
		fclose($fp);
		return true;
	}

	/**
	 * 展品评论审核
	 *
	 * @author yyj 20171026
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibit_comment_list()
	{
		$is_check = request('is_check', 1);
		// 处理排序
		$query = ExhibitComment::orderBy('exhibit_comment.id', 'desc')->join('exhibit', 'exhibit.id', '=', 'exhibit_comment.exhibit_id')->where('exhibit_comment.type', 2);
		// 筛选是评论内容
		if (request('comment')) {
			$query->where('exhibit_comment.comment', 'LIKE', "%" . request('comment') . "%");
		}
		// 筛选是展厅名称
		if (request('exhibit_name')) {
			$query->where('exhibit.exhibit_name', 'LIKE', "%" . request('exhibit_name') . "%");
		}
		// 筛选是否审核
		if ($is_check) {
			$query->where('exhibit_comment.is_check', $is_check);
		}
		// 筛选发表时间时间
		if (request('created_at')) {
			list($begin, $end) = explode(' ~ ', request('created_at'));
			$query->whereBetween('exhibit_comment.created_at', [
				date('Y-m-d H:i:s', strtotime($begin)),
				date('Y-m-d H:i:s', strtotime('+1 day', strtotime($end)))
			]);
		}
		// 取得列表
		$info = $query->leftJoin('users', 'users.uid', '=', 'exhibit_comment.uid')->select('exhibit_comment.*', 'users.username', 'users.nickname', 'exhibit.exhibit_name', 'exhibit.exhibit_img')->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());
		return view('admin.data.exhibit_comment_list', [
			'info' => $info,
		]);
	}

	/**
	 * 通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function pass_check($type, $ids)
	{
		if (request()->ajax()) {
			ExhibitDao::pass_check($type, $ids);
			return $this->success(get_session_url('exhibit_comment_list'));
		}
	}

	/**
	 * 不通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function unpass_check($type, $ids)
	{
		if (request()->ajax()) {
			ExhibitDao::unpass_check($type, $ids);
			return $this->success(get_session_url('exhibition_comment_list'));
		}
	}

	/**
	 * 删除评论
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function del_check($type, $ids)
	{
		if (request()->ajax()) {
			ExhibitDao::del_check($type, $ids);
			return $this->success(get_session_url('exhibition_comment_list'));
		}
	}

}
