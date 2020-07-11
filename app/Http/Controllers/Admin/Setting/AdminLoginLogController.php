<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\AdminLoginLog;

/**
 * Class AdminLoginLogController
 *
 * @author lxp
 * @package App\Http\Controllers\Admin\Setting
 */
class AdminLoginLogController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 管理员登录日志列表
	 *
	 * @author lxp 20180627
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$query = AdminLoginLog::orderBy('login_time', 'DESC')->leftJoin('admin_users', 'admin_users.uid', 'admin_login_log.uid')->select([
			'admin_users.username',
			'login_time',
			'login_ip'
		]);
		// 排除超级管理员
		$query->where('admin_login_log.uid', '<>', 1);
		// 用户名筛选
		if (request()->filled('username')) {
			$query->where('admin_users.username', 'LIKE', '%' . request('username') . '%');
		}
		// 取得列表
		$list = $query->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$list->appends(app('request')->all());

		return view('admin.setting.adminloginlog', [
			'list' => $list
		]);
	}

}