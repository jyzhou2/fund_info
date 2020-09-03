<?php

namespace App\Http\Controllers\Admin\Info;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\JiJinGusuan;
use App\Models\JiJinInfo;
use App\Models\UploadedFile;
use App\Models\Users;
use App\Models\UsersBind;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 用户控制器
 *
 * @author lxp
 * @package App\Http\Controllers\User
 */
class InfoController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}


    /**
     * 推荐基金列表
     *
     * @author lxp 20170111
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $guimo_number = request('guimo_number');
        $query = JiJinGusuan::query();
        if($guimo_number){
            $query->where('guimo_number','>=', $guimo_number);
        }
        $one_week_level = request('one_week_level');
        if($one_week_level){
            $query->orderBy('one_week_level','asc');
        }
        $one_month_level = request('one_week_level');
        if($one_month_level){
            $query->orderBy('one_month_level','asc');
        }
        $three_months_level = request('three_months_level');
        if($three_months_level){
            $query->orderBy('three_months_level','asc');
        }
        $six_months_level = request('six_months_level');
        if($six_months_level){
            $query->orderBy('six_months_level','asc');
        }
        // 取得列表
        $users = $query->select([
            'jjdm',
            'guimo_number',
            'one_week_level',
            'one_month_level',
            'three_months_level',
            'six_months_level',
            'gsl',
            'gsl_update_time',

        ])->paginate(parent::$perpage);
        // 将查询参数拼接到分页链接中
        $users->appends(app('request')->all());

        return view('admin.info.info', [
            'info' => $users,
        ]);
    }

    /**
     * 基金信息列表
     *
     * @author lxp 20170111
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function fundList()
    {
        $query = JiJinInfo::query();
        $jjdm = request('jjdm');
        $jjtype = request('name');
        if($jjdm){
            $query->where('jjdm',$jjdm);
        }
        if($jjtype){
            $query->where('jijin_type','like','%'.$jjtype.'%');
        }
        // 取得列表
        $users = $query->select([
            'jjdm',
            'name',
            'jijin_type',
            'jijin_guimo',
            'jijin_create_day'
        ])->paginate(parent::$perpage);
        // 将查询参数拼接到分页链接中
        $users->appends(app('request')->all());

        return view('admin.info.jijin_info', [
            'info' => $users,
        ]);
    }
}