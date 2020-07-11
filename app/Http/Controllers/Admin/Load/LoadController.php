<?php

namespace App\Http\Controllers\Admin\Load;

use App\Dao\Load\FeedbackDao;
use App\Dao\Load\LoadDao;
use App\Dao\Load\LoadExhibitDao;
use App\Dao\Load\LoadServiceDao;
use App\Dao\Load\NavigationRoadDao;
use App\Dao\Load\PaiDao;
use App\Dao\Load\PositionsDao;
use App\Dao\Load\QuestionDao;
use App\Dao\Load\SvgMapDao;
use App\Dao\Load\ViewGuideDao;
use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\UploadedType;
use Illuminate\Support\Facades\DB;
use App\Models\LoginLog;
use App\Models\AdminUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class LoadController extends BaseAdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 模块装载列表
	 *
	 * @author yyj 20171211
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function load_list()
	{
		//安装列表
		$list = [
			[
				'name' => '随手拍模块',
				'key' => 'pai',
				'status' => 0,
				'des' => ''
			],
			[
				'name' => 'SVG地图管理模块',
				'key' => 'svg_map',
				'status' => 0,
				'des' => ''
			],
			[
				'name' => '展品相关管理模块',
				'key' => 'exhibit',
				'status' => 0,
				'des' => ''
			],
			[
				'name' => '服务设施设置模块',
				'key' => 'service_point',
				'status' => 0,
				'des' => ''
			],
			[
				'name' => '路线导航模块',
				'key' => 'navigation_road',
				'status' => 0,
				'des' => ''
			],
			[
				'name' => '实景导览模块',
				'key' => 'ViewGuide',
				'status' => 0,
				'des' => ''
			],
			[
				'name' => '问卷调查模块',
				'key' => 'Question',
				'status' => 0,
				'des' => ''
			],
			[
				'name' => '意见反馈',
				'key' => 'Feedback',
				'status' => 0,
				'des' => ''
			],
			[
				'name' => '观众定位',
				'key' => 'Positions',
				'status' => 0,
				'des' => ''
			],
		];
		$list = $this->check_status($list);
		return view('admin.load.load_list', [
			'list' => $list
		]);
	}

	/*
	 * 模块文件检测
	 * @author yyj 20171211
	 * */
	private function check_status($info_lost)
	{
		$list = [];
		foreach ($info_lost as $k => $g) {
			switch ($g['key']) {
				//随手拍检测
				case 'pai':
					$list[] = PaiDao::pai_controller('check');
					break;
				//SVG地图管理模块
				case 'svg_map':
					$list[] = SvgMapDao::svg_map_controller('check');
					break;
				//展品相关管理模块
				case 'exhibit':
					$list[] = LoadExhibitDao::exhibit_controller('check');
					break;
				//服务设施设置模块
				case 'service_point':
					$list[] = LoadServiceDao::service_controller('check');
					break;
				//路线导航模块
				case 'navigation_road':
					$list[] = NavigationRoadDao::navigation_road_controller('check');
					break;
				//实景导览模块
				case 'ViewGuide':
					$list[] = ViewGuideDao::view_guide_controller('check');
					break;
				//问卷调查
				case 'Question':
					$list[] = QuestionDao::question_controller('check');
					break;
				//意见反馈
				case 'Feedback':
					$list[] = FeedbackDao::feedback_controller('check');
					break;
				//观众定位
				case 'Positions':
					$list[] = PositionsDao::positions_controller('check');
					break;
			}
		}
		return $list;
	}

	/*
	 * 模块文件安装
	 * @author yyj 20171211
	 * */
	public function install($key)
	{
		switch ($key) {
			//随手拍安装
			case 'pai':
				$r = PaiDao::pai_controller('install');
				break;
			//SVG地图管理模块
			case 'svg_map':
				$r = SvgMapDao::svg_map_controller('install');
				break;
			//展品相关管理模块
			case 'exhibit':
				$r = LoadExhibitDao::exhibit_controller('install');
				break;
			//服务设施设置模块
			case 'service_point':
				$r = LoadServiceDao::service_controller('install');
				break;
			//路线导航模块
			case 'navigation_road':
				$r = NavigationRoadDao::navigation_road_controller('install');
				break;
			//实景导览模块
			case 'ViewGuide':
				$r = ViewGuideDao::view_guide_controller('install');
				break;
			//问卷调查
			case 'Question':
				$r = QuestionDao::question_controller('install');
				break;
			//意见反馈
			case 'Feedback':
				$r = FeedbackDao::feedback_controller('install');
				break;
			//观众定位
			case 'Positions':
				$r = PositionsDao::positions_controller('install');
				break;
			default:
				$r = false;
				break;
		}
		if ($r) {
			return $this->success('load_list', '安装成功');
		} else {
			return $this->error('安装失败');
		}
	}

	/*
	 * 模块文件卸载
	 * @author yyj 20171211
	 * */
	public function uninstall($key)
	{
		switch ($key) {
			//随手拍卸载
			case 'pai':
				$r = PaiDao::pai_controller('uninstall');
				break;
			//SVG地图管理模块
			case 'svg_map':
				$r = SvgMapDao::svg_map_controller('uninstall');
				break;
			//展品相关管理模块
			case 'exhibit':
				$r = LoadExhibitDao::exhibit_controller('uninstall');
				break;
			//服务设施设置模块
			case 'service_point':
				$r = LoadServiceDao::service_controller('uninstall');
				break;
			//路线导航设置模块
			case 'navigation_road':
				$r = NavigationRoadDao::navigation_road_controller('uninstall');
				break;
			//实景导览模块
			case 'ViewGuide':
				$r = ViewGuideDao::view_guide_controller('uninstall');
				break;
			//问卷调查
			case 'Question':
				$r = QuestionDao::question_controller('uninstall');
				break;
			//意见反馈
			case 'Feedback':
				$r = FeedbackDao::feedback_controller('uninstall');
				break;
			//观众定位
			case 'Positions':
				$r = PositionsDao::positions_controller('uninstall');
				break;
			default:
				$r = false;
				break;
		}
		if ($r) {
			return $this->success('load_list', '卸载成功');
		} else {
			return $this->error('卸载失败');
		}
	}

	/*
	 * 模块装载管理卸载
	 *
	 * */
	public function uninstall_controller()
	{
		LoadDao::uninstall();
		return $this->success(route('admin.welcome'), '卸载成功');
	}
}
