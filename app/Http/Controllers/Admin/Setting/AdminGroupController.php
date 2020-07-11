<?php

namespace App\Http\Controllers\Admin\Setting;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\AdminGroup;
use App\Models\AdminUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;

/**
 * 用户组控制器
 *
 * @author lxp 20160621
 * @package App\Http\Controllers\User
 */
class AdminGroupController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 用户组列表
	 *
	 * @author lxp 20170111
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index()
	{
		$query = AdminGroup::orderBy('groupid')->where('groupid', '<>', 1);
		// 取得列表
		$userGroups = $query->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$userGroups->appends(app('request')->all());

		return view('admin.setting.admingroup', [
			'userGroup' => $userGroups
		]);
	}

	/**
	 * 添加用户组
	 *
	 * @author lxp 20160621
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function add()
	{
		//获取当前账号权限
		if(Auth::id()!=1){
			//非root账号只能显示已分配的权限
			$groupid=Auth::user()->groupid;
			$rule=AdminGroup::where('groupid',$groupid)->first()->toArray();
		}
		else{
			$rule=AdminGroup::where('groupid',1)->first()->toArray();
		}
		if($rule['privs'] != 'all'){
			$rule['privs']=json_decode($rule['privs'], true);
		}
		return view('admin.setting.admingroup_form',['rule'=>$rule]);
	}

	/**
	 * 编辑用户组
	 *
	 * @author lxp 20160622
	 * @param int $groupid
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function edit($groupid)
	{
		$ugroup = AdminGroup::findOrFail($groupid);
		// 不允非超级管理员修改顶级管理员权限
		if(Auth::id()!=1&&$ugroup->groupid==2){
			return $this->error('禁止修改超级管理员权限');
		}
		// 不允许修改超级管理员权限
		if ($ugroup->privs == 'all') {
			return redirect(get_session_url('index'));
		}
		// 处理权限数据
		$ugroup->privs = json_decode($ugroup->privs, true);
		//获取当前账号权限
		if(Auth::id()!=1){
			//非root账号只能显示已分配的权限
			$groupid=Auth::user()->groupid;
			$rule=AdminGroup::where('groupid',$groupid)->first()->toArray();
		}
		else{
			$rule=AdminGroup::where('groupid',1)->first()->toArray();
		}
		if($rule['privs'] != 'all'){
			$rule['privs']=json_decode($rule['privs'], true);
		}
		return view('admin.setting.admingroup_form', [
			'ugroup' => $ugroup,
			'rule'=>$rule
		]);
	}

	/**
	 * 保存用户组
	 *
	 * @author lxp 20170204
	 * @param Request $request
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function save(Request $request)
	{
		// 验证
		$this->validate($request, [
			'groupname' => 'required' . (request('groupid') ? '' : '|unique:admin_group'),
			'privs' => 'required'
		]);

		// 保存数据
		$userGroup = AdminGroup::findOrNew(request('groupid', 0));
		$userGroup->groupname = request('groupname');
		$userGroup->privs = json_encode(request('privs'));
		$userGroup->save();

		return $this->success(get_session_url('index'));
	}

	/**
	 * 删除用户组
	 *
	 * @author lxp 20170204
	 * @param $groupid
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function delete($groupid)
	{
		if($groupid==1||$groupid==2){
			return $this->error('禁止删除该角色');
		}
		// 判断当前用户组下是否有用户
		if (AdminUsers::where('groupid', $groupid)->count() > 0) {
			return $this->error('e_usergroup_hasuser');
		}
		// 删除
		AdminGroup::destroy($groupid);

		return $this->success('', 's_del');
	}
}