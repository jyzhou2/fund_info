<?php

namespace App\Http\Controllers\Admin\SvgMapAdmin;

use App\Dao\NavigationRoadDao;
use App\Models\SvgMapTable;
use App\Models\UploadedFile;
use App\Models\VersionList;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseAdminController;

class SvgMapAdminController extends BaseAdminController
{
	//地图名称是否多语种
	private $is_more_language = false;

	//地图名称
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 地图信息列表
	 *
	 * @author yyj 20171026
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function map_list()
	{
		$floor_info = config('floor');
		// 处理排序
		$map_list = SvgMapTable::orderBy('id', 'desc')->get();
		return view('admin.svgmap.svgmap_list', [
			'map_list' => $map_list,
			'floor_info' => $floor_info,
			'floor_arr' => config('floor')
		]);
	}

	/**
	 * 地图编辑
	 *
	 * @author yyj 20171026
	 * @param  int $id 地图id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($id)
	{
		if (request()->isMethod('post')) {
			$this->validate(request(), [
				'map_path' => 'required',
				'map_name' => 'required|max:20',
				'width' => 'required|numeric',
				'height' => 'required|numeric',
				'map_size' => 'required|numeric',
				'map_angle' => 'required|numeric',
			]);
			$map_name_json[1] = request('map_name');
			foreach (config('language') as $k => $g) {
				if ($k != 1) {
					if ($this->is_more_language) {
						$map_name_json[$k] = request('map_name_' . $g['dir']);
					} else {
						$map_name_json[$k] = '';
					}
				}
			}
			$data = [
				'map_name' => request('map_name'),
				'map_path' => request('map_path'),
				'png_map_path' => request('png_map_path'),
				'width' => request('width'),
				'height' => request('height'),
				'map_size' => request('map_size'),
				'map_angle' => request('map_angle'),
				'floor_id' => request('floor_id'),
				'map_name_json' => json_encode($map_name_json)
			];
			if ($id == 'add') {
				$r=SvgMapTable::create($data);
				$map_id=$r->id;
			} else {
				SvgMapTable::where('id', $id)->update($data);
				$map_id=$id;
			}
			//瓦片地图生成
			if(request('png_map_path')){
				$this->generate_tile(request('png_map_path'),$map_id);
			}

			//地图缓存数据更新
			if (file_exists(base_path() . '/app/Dao/NavigationRoadDao.php')) {
				NavigationRoadDao::get_map_cache_map_size($id, 2);
			}
			return $this->success(get_session_url('map_list'));
		} else {
			if ($id != 'add') {
				$map = SvgMapTable::where('id', $id)->first()->toArray();
			} else {
				$map['id'] = 'add';
			}
			return view('admin.svgmap.svgmap_edit', array(
				'map' => $map,
				'floor_arr' => config('floor'),
				'is_more_language' => $this->is_more_language
			));
		}
	}

	/**
	 * 删除地图信息
	 *
	 * @author yyj 20171026
	 * @param  int $id 地图id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function delete($id)
	{
		$r=SvgMapTable::where('id', $id)->delete();
		if($r){
			//删除瓦片地图
			$path = public_path('resource_zip/map') . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
			if (file_exists($path)) {
				deldir($path);
			}
			if (config('exhibit_config.is_version_zip')) {
				$version_id = $this->get_version_id();
				$path = public_path('resource_zip/' . $version_id . '/map') . DIRECTORY_SEPARATOR . $id . DIRECTORY_SEPARATOR;
				if (file_exists($path)) {
					deldir($path);
				}
			}
		}
		return $this->success(get_session_url('index'));
	}

	/**
	 * 地图信息预览
	 *
	 * @author yyj 20171026
	 * @param  int $id 地图id
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function view($id)
	{
		$info = SvgMapTable::where('id', $id)->first();
		return view('admin.svgmap.svgmap_view', array('map' => $info));
	}


	/**
	 * generate_tile
	 * 切割瓦片地图
	 *
	 * @author yyj 20181008
	 * @param string $map_path
	 * @param int $map_id
	 * @return bool
	 */
	private function generate_tile($map_path, $map_id)
	{
		if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
			//echo '这个服务器操作系统为Windows!';
			return false;
		}
		$tilesize = 256;//切割的大小
		$samplesize = 500;//生成最大的img.png
		$basename = 'tmp';
		$path = public_path('resource_zip/map') . DIRECTORY_SEPARATOR . $map_id . DIRECTORY_SEPARATOR;
		$map_path = public_path() .  $map_path;
		if(!file_exists($map_path)){
			return false;
		}
		if (!file_exists($path)) {
			mkdir($path, 0777, true);
		}
		else{
			deldir($path,0);
		}
		exec("convert " . $map_path . " -resize 50%  ".$path."$basename-500.png");
		exec("convert " . $map_path . " -resize 25%  ".$path."$basename-250.png");
		exec("convert " . $map_path . " -resize 12.5%  ".$path."$basename-125.png");
		exec("convert " . $map_path . " -thumbnail $samplesize" . 'x' . $samplesize . " $path" . DIRECTORY_SEPARATOR . "img.png");
		exec("convert " . $map_path . " -crop $tilesize" . 'x' . $tilesize . " -set filename:tile 1_1000_%[fx:page.x/$tilesize]_%[fx:page.y/$tilesize]" . " +repage +adjoin " . $path . "/%[filename:tile].png");
		exec("convert " . "{$path}{$basename}-500.png"  . " -crop $tilesize" . 'x' . $tilesize . " -set filename:tile 1_500_%[fx:page.x/$tilesize]_%[fx:page.y/$tilesize]" . " +repage +adjoin " . $path . "/%[filename:tile].png");
		exec("convert " . "{$path}{$basename}-250.png"  . " -crop $tilesize" . 'x' . $tilesize . " -set filename:tile 1_250_%[fx:page.x/$tilesize]_%[fx:page.y/$tilesize]" . " +repage +adjoin " . $path . "%[filename:tile].png");
		exec("convert " . "{$path}{$basename}-125.png"  . " -crop $tilesize" . 'x' . $tilesize . " -set filename:tile 1_125_%[fx:page.x/$tilesize]_%[fx:page.y/$tilesize]" . " +repage +adjoin " . $path . "%[filename:tile].png");
		//资源打包更新
		if (config('exhibit_config.is_version_zip')) {
			$version_id=$this->get_version_id();
			$path = public_path('resource_zip/'.$version_id.'/map') . DIRECTORY_SEPARATOR . $map_id . DIRECTORY_SEPARATOR;
			if (!file_exists($path)) {
				mkdir($path, 0777, true);
			}
			else{
				deldir($path,0);
			}
			exec("convert " . $map_path . " -resize 50%  ".$path."$basename-500.png");
			exec("convert " . $map_path . " -resize 25%  ".$path."$basename-250.png");
			exec("convert " . $map_path . " -resize 12.5%  ".$path."$basename-125.png");
			exec("convert " . $map_path . " -thumbnail $samplesize" . 'x' . $samplesize . " $path" . DIRECTORY_SEPARATOR . "img.png");
			exec("convert " . $map_path . " -crop $tilesize" . 'x' . $tilesize . " -set filename:tile 1_1000_%[fx:page.x/$tilesize]_%[fx:page.y/$tilesize]" . " +repage +adjoin " . $path . "/%[filename:tile].png");
			exec("convert " . "{$path}{$basename}-500.png"  . " -crop $tilesize" . 'x' . $tilesize . " -set filename:tile 1_500_%[fx:page.x/$tilesize]_%[fx:page.y/$tilesize]" . " +repage +adjoin " . $path . "/%[filename:tile].png");
			exec("convert " . "{$path}{$basename}-250.png"  . " -crop $tilesize" . 'x' . $tilesize . " -set filename:tile 1_250_%[fx:page.x/$tilesize]_%[fx:page.y/$tilesize]" . " +repage +adjoin " . $path . "%[filename:tile].png");
			exec("convert " . "{$path}{$basename}-125.png"  . " -crop $tilesize" . 'x' . $tilesize . " -set filename:tile 1_125_%[fx:page.x/$tilesize]_%[fx:page.y/$tilesize]" . " +repage +adjoin " . $path . "%[filename:tile].png");
		}
		return true;
	}

	/**
	 * 获取当前版本id
	 *
	 * @author yyj 20180306
	 * @return int
	 */
	private function get_version_id()
	{
		$version_id = VersionList::where('type', 0)->value('id');
		if (empty($version_id)) {
			$version_info = VersionList::create(['type' => 0]);
			$version_id = $version_info->id;
		}
		return $version_id;
	}
}
