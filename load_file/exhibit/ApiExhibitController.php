<?php

namespace App\Http\Controllers\Api;

use App\Dao\ExhibitDao;
use App\Dao\SettingDao;
use App\Models\ExhibitComment;
use App\Models\Exhibition;
use App\Models\Exhibit;
use App\Models\ExhibitionLanguage;
use App\Models\ExhibitLanguage;
use App\Models\ExhibitLike;
use App\Models\ExUserVisit;
use App\Models\ExhibitCommentLikelist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 展品导览相关接口
 *
 * @author yyj 20171110
 * @package App\Http\Controllers\Api
 */
class ExhibitController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 获取所有展厅接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibition_list 01.获取所有展厅接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {array} temporary 临时展厅
	 * @apiSuccess {array} theme 主题展厅
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} exhibition_address 展厅地址
	 * @apiSuccess {string} exhibition_img 展厅图片
	 * @apiSuccess {string} floor 楼层
	 * @apiSuccess {int} exhibition_id 展厅id
	 */
	public function exhibition_list()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$data = [];
		//获取临时展厅
		$data['temporary'] = Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->orderBy('order_id', 'ASC')->where('exhibition.type', 2)->where('exhibition.is_show_list', 1)->where('exhibition_language.language', $language)->select('exhibition_language.exhibition_name', 'exhibition_language.exhibition_address', 'exhibition.exhibition_img', 'exhibition.id as exhibition_id','exhibition.floor_id as floor')->get()->toarray();
		foreach ($data['temporary'] as $k => $g) {
			$img_arr = json_decode($g['exhibition_img'], true);
			$data['temporary'][$k]['exhibition_img'] = get_file_url($img_arr['list_img']);
			$data['temporary'][$k]['floor'] = config('floor')[$g['floor']];
		}
		//获取主题展厅
		$data['theme'] = Exhibition::join('exhibition_language', 'exhibition_language.exhibition_id', '=', 'exhibition.id')->where('exhibition.type', 1)->where('exhibition_language.language', $language)->where('exhibition.is_show_list', 1)->select('exhibition_language.exhibition_name', 'exhibition_language.exhibition_address', 'exhibition.exhibition_img', 'exhibition.id as exhibition_id','exhibition.floor_id as floor')->get()->toarray();
		foreach ($data['theme'] as $k => $g) {
			$img_arr = json_decode($g['exhibition_img'], true);
			$data['theme'][$k]['exhibition_img'] = get_file_url($img_arr['list_img']);
			$data['theme'][$k]['floor'] = config('floor')[$g['floor']];
		}
		return response_json(1, $data);
	}

	/**
	 * 展品列表接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibit_list 02.展品列表接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {int} exhibition_id 展厅编号
	 * @apiParam {int} skip 数据偏移量默认0
	 * @apiParam {int} take 查询数量默认10
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} exhibit_list_img 展品图片
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {int} look_num 浏览数量
	 * @apiSuccess {int} like_num 点赞数量
	 */
	public function exhibit_list()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'exhibition_id' => 'required|min:0|integer',
			'skip' => 'required|min:0|integer',
			'take' => 'required|min:0|integer',
		]);
		$language = request('language', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$exhibition_id = request('exhibition_id', 0);
		$data = [];
		$exhibit_list = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->orderBy('exhibit.order_id', 'asc')->where('exhibit_language.language', $language)->where('exhibit.is_show_list', 1)->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit.look_num', 'exhibit.like_num')->where('exhibit.exhibition_id', $exhibition_id)->orderBy('exhibit.order_id', 'asc')->skip($skip)->take($take)->get()->toArray();
		foreach ($exhibit_list as $k => $g) {
			$imgs = json_decode($g['exhibit_img'], true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$data[$k]['exhibit_list_img'] = get_file_url($imgs);
			$data[$k]['exhibit_id'] = $g['exhibit_id'];
			$data[$k]['exhibit_name'] = $g['exhibit_name'];
			$data[$k]['look_num'] = $g['look_num'];
			$data[$k]['like_num'] = $g['like_num'];
		}
		return response_json(1, $data);
	}

	/**
	 * 展品详情接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibit_info 03.展品详情接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {int} exhibit_id 展品编号
	 * @apiParam {string} [api_token] token(登录后上传)
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {array} exhibit_imgs 展品图片
	 * @apiSuccess {string} exhibit_icon1 地图页图片(亮)
	 * @apiSuccess {string} exhibit_icon2 地图页图片(暗)
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {string} audio 音频地址
	 * @apiSuccess {string} content_url 讲解词url
	 * @apiSuccess {string} knowledge_url 科普问答url
	 * @apiSuccess {string} share_url 分享页url
	 * @apiSuccess {int} is_like 是否点赞1已点赞0未点赞
	 * @apiSuccess {int} is_collection 是否收藏1已收藏0未收藏
	 * @apiSuccess {int} map_id 地图编号
	 * @apiSuccess {int} x x轴坐标
	 * @apiSuccess {int} y y轴坐标
	 * @apiSuccess {string} exhibition_name 展厅名
	 * @apiSuccess {string} floor 所在楼层
	 */
	public function exhibit_info()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'exhibit_id' => 'required|min:0|integer',
		]);
		$p = request('p', 'a');
		$language = request('language', 1);
		$exhibit_id = request('exhibit_id', 0);
		$exhibit_info = Exhibit::join('exhibit_language', function ($join) use ($language) {
			$join->on('exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', '=', $language);
		})->join('exhibition', 'exhibition.id', '=', 'exhibit.exhibition_id')->join('exhibition_language', function ($join) use ($language) {
			$join->on('exhibition.id', '=', 'exhibition_language.exhibition_id')->where('exhibition_language.language', '=', $language);
		})->where('exhibit.id', $exhibit_id)->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit_language.audio', 'exhibit.map_id', 'exhibit.x', 'exhibit.y', 'exhibition_language.exhibition_name', 'exhibition.floor_id')->first();
		$data = [];
		if (!empty($exhibit_info)) {
			$data['exhibit_id'] = $exhibit_info->exhibit_id;
			$data['exhibit_name'] = $exhibit_info->exhibit_name;
			$data['exhibit_imgs'] = get_file_url(json_decode($exhibit_info->exhibit_img, true)['exhibit_imgs']);
			$data['exhibit_icon1'] = get_file_url(json_decode($exhibit_info->exhibit_img, true)['exhibit_icon1']);
			$data['exhibit_icon2'] = get_file_url(json_decode($exhibit_info->exhibit_img, true)['exhibit_icon2']);
			$data['audio'] = get_file_url($exhibit_info->audio);
			$data['map_id'] = $exhibit_info->map_id;
			$data['x'] = $exhibit_info->x;
			$data['y'] = $exhibit_info->y;
			$data['content_url'] = get_file_url('/api/exhibit_content_info/' . $language . '/' . $exhibit_id . '?p=' . $p . '&language=' . $language);
			$data['knowledge_url'] = get_file_url('/api/exhibit_knowledge_info/' . $language . '/' . $exhibit_id . '?p=' . $p . '&language=' . $language);
			$data['share_url'] = get_file_url('/api/exhibit_share_info/' . $language . '/' . $exhibit_id . '?p=' . $p . '&language=' . $language);
			$data['exhibition_name'] = $exhibit_info->exhibition_name;
			$data['floor'] = config('floor')[$exhibit_info->floor_id];
			$user = Auth::user();
			if (false == empty($user)) {
				$uid = $user->uid;
				$data['is_like'] = ExhibitLike::where('uid', $uid)->where('exhibit_id', $data['exhibit_id'])->where('type', 1)->count();
				$data['is_collection'] = ExhibitLike::where('uid', $uid)->where('exhibit_id', $data['exhibit_id'])->where('type', 2)->count();
			} else {
				$data['is_like'] = 0;
				$data['is_collection'] = 0;
			}
			return response_json(1, $data);
		} else {
			return response_json(0, '', 'error exhibit_id');
		}
	}

	/**
	 * 展品详情页
	 *
	 * @author yyj 20180321
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @param  int $exhibit_id 展品编号
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibit_content_info($language, $exhibit_id)
	{
		$info = ExhibitLanguage::where('language', $language)->where('exhibit_id', $exhibit_id)->select('content', 'exhibit_name')->first();
		return view('api.exhibit.exhibit_content_info', array(
			'info' => $info,
			'language' => $language,
		));
	}

	/**
	 * 展品科普知识页
	 *
	 * @author yyj 20180321
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @param  int $exhibit_id 展品编号
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibit_knowledge_info($language, $exhibit_id)
	{
		$info = ExhibitLanguage::where('language', $language)->where('exhibit_id', $exhibit_id)->select('knowledge', 'exhibit_name')->first();
		return view('api.exhibit.exhibit_knowledge_info', array(
			'info' => $info,
			'language' => $language,
		));
	}

	/**
	 * 展品分享页
	 *
	 * @author yyj 20180321
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @param  int $exhibit_id 展品编号
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function exhibit_share_info($language, $exhibit_id)
	{
		$info = Exhibit::join('exhibit_language', 'exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', $language)->where('exhibit.id', $exhibit_id)->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit_language.knowledge', 'exhibit_language.content', 'exhibit_language.audio')->first();
		return view('api.exhibit.exhibit_share_info', array(
			'info' => $info,
			'language' => $language,
		));
	}

	/**
	 * 展品点赞收藏操作接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /do_like 04.展品点赞收藏添加取消操作接口（系统自动判断执行添加还是取消）
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} exhibit_id 展品编号
	 * @apiParam {int} type 类别1点赞2收藏
	 * @apiParam {string} api_token token
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function do_like()
	{
		$this->validate([
			'exhibit_id' => 'required|min:0|integer',
			'type' => 'required|min:0|integer',
		]);
		$exhibit_id = request('exhibit_id');
		$type = request('type');
		$uid = Auth::user()->uid;
		$is_set = ExhibitLike::where('uid', $uid)->where('exhibit_id', $exhibit_id)->where('type', $type)->first();
		if (empty($is_set)) {
			$r = ExhibitLike::create([
				'uid' => $uid,
				'exhibit_id' => $exhibit_id,
				'type' => $type
			]);
			if ($type == 1) {
				Exhibit::where('id', $exhibit_id)->increment('like_num');
			} elseif ($type == 2) {
				Exhibit::where('id', $exhibit_id)->increment('collection_num');
			}
		} else {
			$r = ExhibitLike::where('uid', $uid)->where('exhibit_id', $exhibit_id)->where('type', $type)->delete();
			if ($type == 1) {
				Exhibit::where('id', $exhibit_id)->decrement('like_num');
			} elseif ($type == 2) {
				Exhibit::where('id', $exhibit_id)->decrement('collection_num');
			}
		}
		if ($r) {
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}

	/**
	 * 展厅/展品评论接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {POST} /exhibit_comment 05.展厅/展品评论接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} exhibition_id 展厅编号，展品评论时传0
	 * @apiParam {int} exhibit_id 展品编号，展厅评论时传0
	 * @apiParam {int} type 类别1展厅评论2展品评论
	 * @apiParam {string} api_token token
	 * @apiParam {string} comment 评论内容
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function exhibit_comment()
	{
		$this->validate([
			'exhibit_id' => 'required|min:0|integer',
			'exhibition_id' => 'required|min:0|integer',
			'type' => 'required|min:0|integer',
			'comment' => 'required|string|max:500',
		]);
		$exhibit_id = request('exhibit_id', 0);
		$exhibition_id = request('exhibition_id', 0);
		$type = request('type');
		$uid = Auth::user()->uid;
		$comment = request('comment', '');
		$r = ExhibitComment::create([
			'exhibit_id' => $exhibit_id,
			'exhibition_id' => $exhibition_id,
			'type' => $type,
			'uid' => $uid,
			'comment' => $comment,
			'is_check' => 1,
			'like_num' => 0,
		]);
		if ($r) {
			if ($type == 2) {
				Exhibit::where('id', $exhibit_id)->increment('comment_num');
			}
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}

	/**
	 * 展厅/展品评论列表
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /comment_list 06.展厅/展品评论列表
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} ex_id 展厅编号或展品编号
	 * @apiParam {int} type 类别1展厅评论2展品评论
	 * @apiParam {int} skip 数据偏移量默认0
	 * @apiParam {int} take 查询数量默认10
	 * @apiParam {string} [api_token] token
	 * @apiSuccess {array} data 列表信息
	 * @apiSuccess {int} comment_id 评论id
	 * @apiSuccess {int} like_num 点赞数量
	 * @apiSuccess {string} datetime 评论时间
	 * @apiSuccess {string} nickname 昵称
	 * @apiSuccess {string} avatar 头像
	 * @apiSuccess {string} is_like 是否点赞
	 */
	public function comment_list()
	{
		$this->validate([
			'ex_id' => 'required|min:0|integer',
			'type' => 'required|min:0|integer',
			'skip' => 'required|min:0|integer',
			'take' => 'required|min:0|integer',
		]);
		$type = request('type', 1);
		$ex_id = request('ex_id', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$uid = 0;
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
		}
		$data = ExhibitDao::comment_list($type, $skip, $take, $ex_id, $uid);
		return response_json(1, $data);
	}

	/**
	 * 评论点赞取消接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /comment_do_like 07.评论点赞取消接口（系统自动判断执行添加还是取消）
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} comment_id 评论编号
	 * @apiParam {string} api_token token
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function comment_do_like()
	{
		$this->validate([
			'comment_id' => 'required|min:0|integer',
		]);
		$comment_id = request('comment_id');
		$uid = Auth::user()->uid;
		$is_set = ExhibitCommentLikelist::where('uid', $uid)->where('comment_id', $comment_id)->first();
		if (empty($is_set)) {
			$r = ExhibitCommentLikelist::create([
				'uid' => $uid,
				'comment_id' => $comment_id
			]);
			ExhibitComment::where('id', $comment_id)->increment('like_num');
		} else {
			$r = ExhibitCommentLikelist::where('uid', $uid)->where('comment_id', $comment_id)->delete();
			ExhibitComment::where('id', $comment_id)->decrement('like_num');
		}
		if ($r) {
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}

	/**
	 * 展品浏览收听接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /visit_exhibit 08.展品浏览收听接口（浏览展品和播放语音时调用）
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} exhibit_id 展品编号
	 * @apiParam {string} [api_token] token
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function visit_exhibit()
	{
		$this->validate([
			'exhibit_id' => 'required|min:0|integer',
		]);
		$exhibit_id = request('exhibit_id', 1);
		$uid = 0;
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
		}
		//展品浏览量+1
		Exhibit::where('id', $exhibit_id)->increment('look_num');
		if ($uid) {
			ExhibitLike::create([
				'uid' => $uid,
				'exhibit_id' => $exhibit_id,
				'type' => 3
			]);
		}
		//展品收听量+1
		$r = Exhibit::where('id', $exhibit_id)->increment('listen_num');
		if ($uid) {
			$u_ex_info = ExUserVisit::where('uid', $uid)->first();
			if (empty($u_ex_info)) {
				ExUserVisit::create([
					'uid' => $uid,
					'use_time' => 0,
					'listen_num' => 1
				]);
			} else {
				ExUserVisit::where('uid', $uid)->increment('listen_num');
			}
		}
		if ($r) {
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}

	/**
	 * 展品搜索接口
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /exhibit_search 09.展品搜索接口
	 * @apiGroup Exhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {string} keyword 展品名或编号
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiSuccess {json} data 数据详情
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {string} exhibit_num 展品编号
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} floor 所在楼层
	 */
	public function exhibit_search()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'keyword' => 'required|string|max:255',
		]);
		$language = request('language', 1);
		$keyword = request('keyword', 1);
		$exhibit_list = Exhibit::join('exhibit_language', function ($join) use ($language) {
			$join->on('exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', '=', $language);
		})->join('exhibition', 'exhibition.id', '=', 'exhibit.exhibition_id')->join('exhibition_language', function ($join) use ($language) {
			$join->on('exhibition.id', '=', 'exhibition_language.exhibition_id')->where('exhibition_language.language', '=', $language);
		})->where('exhibit.is_show_list', 1)->where(function ($query) use ($keyword) {
			$query->where('exhibit.exhibit_num', 'like', '%' . $keyword . '%')->orwhere('exhibit_language.exhibit_name', 'like', '%' . $keyword . '%');
		})->select('exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.exhibit_num', 'exhibit.id as exhibit_id', 'exhibition_language.exhibition_name', 'exhibition.floor_id')->orderBy('exhibit.look_num', 'desc')->get();
		$data = [];
		foreach ($exhibit_list as $k => $g) {
			$imgs = json_decode($g->exhibit_img, true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$data[$k]['exhibit_name'] = $g->exhibit_name;
			$data[$k]['exhibit_num'] = $g->exhibit_num;
			$data[$k]['exhibit_id'] = $g->exhibit_id;
			$data[$k]['exhibit_list_img'] = get_file_url($imgs);
			$data[$k]['exhibition_name'] = $g->exhibition_name;
			$data[$k]['floor'] = config('floor')[$g->floor_id];
		}
		return response_json(1, $data);
	}
}