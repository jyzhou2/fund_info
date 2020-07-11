<?php

namespace App\Http\Controllers\Admin\ViewGuide;

use App\Dao\SettingDao;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Exhibit;
use App\Models\SvgMapTable;
use App\Models\ViewGuide;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ViewGuideController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 实景导览列表
	 *
	 * @author yyj 20180703
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function view_guide_list()
	{
		// 处理排序
		$query = ViewGuide::orderBy('id', 'desc');
		// 筛选是名称
		if (request('exhibit_name')) {
			$query->where('exhibit_name', 'LIKE', "%" . request('exhibit_name') . "%");
		}
		// 筛选地图类别
		if (request('map_id')) {
			$query->where('map_id', request('map_id'));
		}
		// 取得列表
		$info = $query->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());

		$list_info = ViewGuide::select('exhibit_name', 'id')->orderBy('exhibit_name', 'asc')->get()->toArray();
		$map_info = SvgMapTable::orderBy('id', 'asc')->get();
		return view('admin.view_guide.view_guide_list', [
			'info' => $info,
			'map_info' => $map_info,
			'list_info' => $list_info
		]);

	}

	/**
	 * 实景导览编辑
	 *
	 * @author yyj 20171027
	 * @param  int $id 实景导览id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function view_guide_edit($id)
	{
		if (request()->isMethod('post')) {
			$this->validate(request(), [
				'exhibit_id' => 'required',
				'imgs' => 'required',
			]);
			$img = request('imgs');
			$exhibit_id = request('exhibit_id');
			if ($id == 'add') {
				$old_img = [];
				$old_exhibit_id = 0;
			} else {
				$old_info = ViewGuide::where('id', $id)->select('img', 'exhibit_id')->first();
				$old_img = json_decode($old_info->img, true);
				$old_exhibit_id = json_decode($old_info->exhibit_id, true);
			}
			if (empty($exhibit_id)) {
				return $this->error('请选择要关联的展品');
			}
			if (empty($img)) {
				return $this->error('请上传实景导览关联图片');
			}
			//获取关联展品的信息
			$exhibit_info = Exhibit::where('id', $exhibit_id)->select('exhibit_name', 'map_id')->first();
			if (empty($exhibit_info)) {
				return $this->error('所选择的展品不存在，请刷新页面后再试');
			}
			//判断展品是否被关联
			$is_set = ViewGuide::where('id', '<>', $id)->where('exhibit_id', $exhibit_id)->count();
			if ($is_set) {
				return $this->error('该展品已被关联');
			}
			$map_id = $exhibit_info->map_id;
			$data = [
				'img' => json_encode($img),
				'map_id' => $map_id,
				'exhibit_id' => $exhibit_id,
				'exhibit_name' => $exhibit_info->exhibit_name,
			];
			//基本信息入库
			if ($id == 'add') {
				$r = ViewGuide::create($data);
				$id = $r->id;
			} else {
				$r = ViewGuide::where('id', $id)->update($data);
			}
			if ($r) {
				$file_path = base_path() . '/public/uploadfiles/viewguide_resource/' . $map_id;
				if (!file_exists($file_path)) {
					mkdir($file_path, 0777, true);
				}
				//图片资源更新
				if (json_encode($img) != json_encode($old_img) || $exhibit_id != $old_exhibit_id) {
					//删除旧图片
					foreach ($old_img as $k => $g) {
						$temp = explode(".", $g);
						$extension = end($temp);
						if ($k == 0) {
							$old_img_path = $file_path . '/' . $old_exhibit_id . '.' . $extension;
						} else {
							$old_img_path = $file_path . '/' . $old_exhibit_id . '-' . $k . '.' . $extension;
						}
						if (file_exists($old_img_path)) {
							unlink($old_img_path);
						}
					}
				}
				$arr_zip_img = [];
				foreach ($img as $k => $g) {
					$temp = explode(".", $g);
					$extension = end($temp);
					$img_path = base_path() . '/public' . $g;
					if (file_exists($img_path)) {
						if ($k == 0) {
							$arr_zip_img[] = $exhibit_id . '.' . $extension;
							copy($img_path, $file_path . '/' . $exhibit_id . '.' . $extension);
						} else {
							$arr_zip_img[] = $exhibit_id . '-' . $k . '.' . $extension;
							copy($img_path, $file_path . '/' . $exhibit_id . '-' . $k . '.' . $extension);
						}
					}
				}
				$arr_zip_img = json_encode($arr_zip_img);
				ViewGuide::where('id', $id)->update(['zip_img' => $arr_zip_img]);
			}
			return $this->success(get_session_url('view_guide_list'));
		} else {
			$info = [];
			if ($id !== 'add') {
				$info = ViewGuide::where('id', $id)->first()->toArray();
				$info['img'] = json_decode($info['img'], true);
			} else {
				$info['exhibit_id'] = 'add';
			}
			$list_info = Exhibit::select('exhibit_name', 'id')->orderBy('exhibit_name', 'asc')->get()->toArray();
			return view('admin.view_guide.view_guide_edit', array(
				'info' => $info,
				'list_info' => $list_info,
				'id' => $id
			));
		}
	}

	/**
	 * 实景导览删除
	 *
	 * @author yyj 20171109
	 * @param  int $id 服务设施id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function view_guide_delete($id)
	{
		$old_info = ViewGuide::where('id', $id)->select('img', 'map_id', 'exhibit_id')->first();
		$arr = json_decode($old_info->img, true);
		$r = ViewGuide::where('id', $id)->delete();
		if ($r) {
			//删除旧图片
			$file_path = base_path() . '/public/uploadfiles/viewguide_resource/' . $old_info->map_id;
			foreach ($arr as $k => $g) {
				$img_path = base_path() . '/public' . $g;
				if (file_exists($img_path)) {
					unlink($img_path);
				}
				$temp = explode(".", $g);
				$extension = end($temp);
				if ($k == 0) {
					$old_img_path = $file_path . '/' . $old_info->exhibit_id . '.' . $extension;
				} else {
					$old_img_path = $file_path . '/' . $old_info->exhibit_id . '-' . $k . '.' . $extension;
				}
				if (file_exists($old_img_path)) {
					unlink($old_img_path);
				}
			}
		}
		return $this->success(get_session_url('view_guide_list'));
	}

	/*
	 * 实景导览资源打包
	 *
	 * */
	public function resource_zip()
	{
		//获取有资源的地图数据
		$map_id_arr = ViewGuide::groupBy('map_id')->pluck('map_id')->toArray();
		if (request()->isMethod('post')) {
			$arr_id = request('arr_id');
			$arr_total_num = request('total_num');
			$arr_total_size = request('total_size');
			$zip_key = 'viewguide_zip_info';
			//重置缓存数据
			if ($arr_id == 0) {
				if (Cache::has($zip_key)) {
					Cache::forget($zip_key);
				}
			}
			if (isset($map_id_arr[$arr_id])) {
				//资源计算
				$arr['type'] = 'next';
				$arr['id'] = $arr_id + 1;
				$map_id = $map_id_arr[$arr_id];
				$path = base_path() . '/public/uploadfiles/viewguide_resource/' . $map_id;
				$path = str_replace("\\", '/', $path);
				$arr['file_list'] = [];
				$arr['map_id'] = $map_id;
				$arr['num'] = 0;
				$arr['size'] = 0;
				$arr['total_num'] = $arr_total_num;
				$arr['total_size'] = $arr_total_size;
				$dh = opendir($path);
				while ($file = readdir($dh)) {
					$fullpath = $path . "/" . $file;
					//过滤需要排除的文件，过滤文件名含有中文的文件
					if ($file != "." && $file != ".." && strstr($file, 'zip') === false && !preg_match('/[^\x00-\x80]/', $fullpath)) {
						$arr['file_list'][] = $fullpath;
						$arr['num'] = $arr['num'] + 1;
						$arr['size'] = $arr['size'] + abs(filesize($fullpath));
						$arr['total_num'] = $arr['total_num'] + 1;
						$arr['total_size'] = $arr['total_size'] + abs(filesize($fullpath));
					}
				}
				closedir($dh);
				$arr['size'] = sizecount($arr['size']);
				$arr['total_size_info'] = sizecount($arr['total_size']);
				if (Cache::has($zip_key)) {
					$zip_info = Cache::get($zip_key);
				} else {
					$zip_info = [];
				}
				$zip_info[$map_id] = $arr;
				Cache::forever($zip_key, $zip_info);
			} else {
				$arr['type'] = 'end';
				//删除资源包
				foreach ($map_id_arr as $k => $g) {
					$zip_path = base_path() . "/public/uploadfiles/viewguide_resource/" . $g . ".zip";
					if (file_exists($zip_path)) {
						unlink($zip_path);
					}
				}
				$total_zip_path = base_path() . "/public/uploadfiles/viewguide_resource/resource.zip";
				if (file_exists($total_zip_path)) {
					unlink($total_zip_path);
				}
			}
			unset($arr['file_list']);
			return response()->json($arr);
		} else {
			//获取楼层数据
			$map_info = SvgMapTable::whereIn('id', $map_id_arr)->select('id as map_id', 'map_name')->get()->toArray();
			//判断是否需要打包更新
			$img_arr = ViewGuide::select('img')->get()->toArray();
			$resource_md5 = SettingDao::getSetting('viewguide_update_md5');
			if ($resource_md5 == md5(json_encode($img_arr))) {
				$is_need_update = false;
			} else {
				$is_need_update = true;
			}
			return view('admin.view_guide.resource_zip', array(
				'is_need_update' => $is_need_update,
				'map_info' => $map_info
			));
		}
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
			//获取有资源的地图数据
			$map_id_arr = ViewGuide::groupBy('map_id')->pluck('map_id')->toArray();
			$zip_key = 'viewguide_zip_info';
			if (!Cache::has($zip_key)) {
				$arr['type'] = 'error';
				return response()->json($arr);
			} else {
				$arr_id = request('arr_id');
				$arr_img_mun = request('arr_img_mun');
				$arr_total_num = request('arr_total_num');
				$zip_info = Cache::get($zip_key);
				if (empty($zip_info) || !isset($map_id_arr[$arr_id])) {
					Cache::forget($zip_key);
					$arr['type'] = 'end';
					//资源更新记录
					$img_arr = ViewGuide::select('img')->get()->toArray();
					SettingDao::setSetting('viewguide_update_md5', md5(json_encode($img_arr)));
					return response()->json($arr);
				} else {
					$map_id = $map_id_arr[$arr_id];
					$path = base_path() . '/public/uploadfiles/viewguide_resource/' . $map_id . '/';
					$local_path = str_replace("\\", '/', $path);
					$zip_path = base_path() . "/public/uploadfiles/viewguide_resource/" . $map_id . ".zip";
					$total_zip_path = base_path() . "/public/uploadfiles/viewguide_resource/resource.zip";
					//资源打包
					if (isset($zip_info[$map_id])) {
						$zip_file_num = 10;//单次ajax打包的图片数量
						if (count($zip_info[$map_id]['file_list']) > 0) {
							$zip = new \ZipArchive();
							//单包资源打包
							if ($zip->open($zip_path, \ZipArchive::CREATE) === TRUE) {
								//创建空文件夹,必须创建，自动生成的文件夹缺少时间属性手机无法解压
								$zip->addEmptyDir(str_replace($local_path, '', $map_id));
								foreach ($zip_info[$map_id]['file_list'] as $k => $g) {
									if ($k + 1 >= $zip_file_num) {
										break;
									} else {
										//unset($zip_info[$map_id]['file_list'][$k]);
										$arr_img_mun = $arr_img_mun + 1;
										$arr_total_num = $arr_total_num + 1;
										$zip->addFile($g, $map_id . '/' . str_replace($local_path, '', $g));
									}
								}
								$zip->close(); //关闭处理的zip文件
							}
							//整包资源打包
							if ($zip->open($total_zip_path, \ZipArchive::CREATE) === TRUE) {
								//创建空文件夹,必须创建，自动生成的文件夹缺少时间属性手机无法解压
								$zip->addEmptyDir(str_replace($local_path, '', $map_id));
								foreach ($zip_info[$map_id]['file_list'] as $k => $g) {
									if ($k + 1 >= $zip_file_num) {
										break;
									} else {
										unset($zip_info[$map_id]['file_list'][$k]);
										$zip->addFile($g, $map_id . '/' . str_replace($local_path, '', $g));
									}
								}
								$zip->close(); //关闭处理的zip文件
							}
							rsort($zip_info[$map_id]['file_list']);
						} else {
							unset($zip_info[$map_id]);
						}
						$arr['total_num'] = $arr_total_num;
						$arr['img_mun'] = $arr_img_mun;
						$arr['id'] = $arr_id;
					} else {
						$arr['total_num'] = $arr_total_num;
						$arr['img_mun'] = $arr_img_mun;
						$arr['id'] = $arr_id + 1;
					}
					Cache::forever($zip_key, $zip_info);
					$arr['type'] = 'next';
					return response()->json($arr);
				}
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
		$path = base_path() . "/public/uploadfiles/viewguide_resource/resource.zip";
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

}
