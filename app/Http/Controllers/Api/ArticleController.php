<?php

namespace App\Http\Controllers\Api;

use App\Dao\SmsVerifyDao;
use App\Dao\UploadedFileDao;
use App\Dao\UsersDao;
use App\Exceptions\ApiErrorException;
use App\Models\Article;
use App\Models\JiJinGusuan;
use App\Models\JiJinInfo;
use App\Models\JiJinTheme;
use App\Models\Users;
use App\Models\UsersBind;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ArticleController extends Controller
{
    public function __construct()
    {
        parent::_init();
    }

    public function articleList(){
        $type = \request('cate_id');
        $list = Article::where('cate_id', $type)->select('article_id','title','sub_title','default_img','updated_at')->orderBy('article_id','desc')->get();
        return response_json(1, $list);
    }

    public function articleDetail(){
        $article_id = \request('article_id');
        $article = Article::find($article_id);
        return response_json(1, $article);
    }
}
