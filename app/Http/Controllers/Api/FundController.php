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
        if (!is_array($themes)) {
            $themes = [$themes];
        }
        $guimo = request('guimo');
        $raw_jjdms = JiJinGusuan::select('jjdm')->get()->pluck('jjdm')->all();
        $jjdms = [];
        foreach ($themes as $theme) {
            $cur_jjdm = JiJinTheme::where('name', 'like', '%' . $theme . '%')->select('jjdm')->get()->pluck('jjdm')->all();
            if ($cur_jjdm) {
                $jjdms = array_merge($jjdms, $cur_jjdm);
            }
        }
        // 获得基金列表
        if ($guimo) {
            if($jjdms){
                $jjdms = JiJinGusuan::where('guimo_number', '>=', $guimo)->whereIn('jjdm',$jjdms)->select('jjdm')->get()->pluck('jjdm')->all();
            }
        }
        if(empty($jjdms)){
            $jjdms = $raw_jjdms;
        }
        $info = JiJinGusuan::join('jijininfo','jijininfo.jjdm','=','jijingusuan.jjdm')->whereIn('jijingusuan.jjdm',$jjdms)->orderBy('one_week_level')->get();
        return response_json(1,$info);
    }

}
