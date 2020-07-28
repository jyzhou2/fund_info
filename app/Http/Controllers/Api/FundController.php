<?php

namespace App\Http\Controllers\Api;

use App\Dao\SmsVerifyDao;
use App\Dao\UploadedFileDao;
use App\Dao\UsersDao;
use App\Exceptions\ApiErrorException;
use App\Models\JiJinGusuan;
use App\Models\JiJinInfo;
use App\Models\JiJinTheme;
use App\Models\Users;
use App\Models\UsersBind;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class FundController extends Controller
{
    public function __construct()
    {
        parent::_init();
    }

    /**
     * 获得对应的基金列表
     */
    public function getCorrectFund()
    {
        $themes = request('theme');

        $guimo = request('guimo');


        if (empty($themes) && empty($guimo)) {
            return response_json(1, []);
        }
        $query = JiJinGusuan::join('jijininfo', 'jijininfo.jjdm', '=', 'jijingusuan.jjdm');
        // 获得基金列表

        if ($guimo) {
            $guimo_jjdms = JiJinGusuan::where('guimo_number', '>=', $guimo)->select('jjdm')->get()->pluck('jjdm')->all();
            $query->whereIn('jijingusuan.jjdm', $guimo_jjdms);
        }

        if ($themes) {
            $theme_jjdm = JiJinTheme::where('name', 'like', '%' . $themes . '%')->select('jjdm')->get()->pluck('jjdm')->all();
            $query->whereIn('jijingusuan.jjdm', $theme_jjdm);

        }

        $info = $query->orderBy('recommand', 'desc')->get();
        foreach ($info as $k => $item) {
            $info[$k]->one_week_level = round($info[$k]->one_week_level, 2);
            $info[$k]->one_month_level = round($info[$k]->one_month_level, 2);
            $info[$k]->three_months_level = round($info[$k]->three_months_level, 2);
            $info[$k]->six_months_level = round($info[$k]->six_months_level, 2);
        }
        return response_json(1, $info);
    }

    /**
     * 获得基金主题列表
     */
    public function getThemeList()
    {
        $name_list = JiJinTheme::select(DB::Raw('distinct(name) as name'))->get()->pluck('name')->all();
        $res = [];
        foreach ($name_list as $name) {
            $jjdms = JiJinTheme::where('name', $name)->select('jjdm')->get()->pluck('jjdm')->all();
            if(empty($jjdms)){
                $res[] = ['title' => $name, 'label' => 0];
            }
            else{
                $res[] = ['title' => $name, 'label' => JiJinGusuan::whereIn('jjdm', $jjdms)->count()];

            }
        }
        return response_json(1, $res);

    }

}
