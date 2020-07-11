<?php

namespace App\Http\Controllers\Api;

use App\Models\ExhibitComment;
use App\Models\ExhibitLike;
use App\Models\ExhibitCommentLikelist;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * 展品导览相关接口
 *
 * @author yyj 20171112
 * @package App\Http\Controllers\Api
 */
class MyExhibitController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 我的浏览记录
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /my_looked 01.我的浏览记录
	 * @apiGroup MyExhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {string} api_token token
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {int} skip 数据偏移量默认0
	 * @apiParam {int} take 查询数量默认10
	 * @apiSuccess {string} date 日期
	 * @apiSuccess {array} list 展品列表
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} exhibit_img 图片url
	 * @apiSuccess {string} exhibit_id 展品id
	 * @apiSuccess {string} datetime 浏览时间
	 * @apiSuccess {string} exhibition_name 展厅名称
	 * @apiSuccess {string} floor 所在楼层
	 */
	public function my_looked()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'skip' => 'required|man:0|integer',
			'take' => 'required|man:0|integer',
		]);
		$uid = Auth::user()->uid;
		$language = request('language', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$infolist = ExhibitLike::where('exhibit_like.uid', $uid)->join('exhibit', 'exhibit.id', '=', 'exhibit_like.exhibit_id')->join('exhibit_language', function ($join) use ($language) {
			$join->on('exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', '=', $language);
		})->join('exhibition', 'exhibition.id', '=', 'exhibit.exhibition_id')->join('exhibition_language', function ($join) use ($language) {
			$join->on('exhibition.id', '=', 'exhibition_language.exhibition_id')->where('exhibition_language.language', '=', $language);
		})->where('exhibit_like.type', 3)->skip($skip)->take($take)->select('exhibit_like.created_at as datetime', 'exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibition_language.exhibition_name', 'exhibition.floor_id as floor')->orderBy('exhibit_like.id', 'desc')->get()->toArray();
		//return response_json(1, $infolist);
		if (empty($infolist)) {
			$list = [];
		} else {
			$a = 0;
			$imgs = json_decode($infolist[0]['exhibit_img'], true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$infolist[0]['exhibit_img'] = get_file_url($imgs);
			$infolist[0]['floor'] = config('floor')[$infolist[0]['floor']];
			$list[$a]['date'] = date('Y.m.d', strtotime($infolist[0]['datetime']));
			$infolist[0]['datetime'] = date('H:i:s', strtotime($infolist[0]['datetime']));
			$list[$a]['list'][0] = $infolist[0];
			$n = count($infolist);
			$k = 1;
			for ($i = 1; $i < $n; $i++) {
				$imgs = json_decode($infolist[$i]['exhibit_img'], true);
				$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
				$infolist[$i]['exhibit_img'] = get_file_url($imgs);
				$infolist[$i]['floor'] = config('floor')[$infolist[$i]['floor']];
				if ($list[$a]['date'] !== date('Y.m.d', strtotime($infolist[$i]['datetime']))) {
					$a = $a + 1;
					$k = 0;
				}
				$list[$a]['date'] = date('Y.m.d', strtotime($infolist[$i]['datetime']));
				$infolist[$i]['datetime'] = date('H:i:s', strtotime($infolist[$i]['datetime']));
				$list[$a]['list'][$k] = $infolist[$i];
				$k = $k + 1;
			}
		}
		return response_json(1, $list);
	}

	/**
	 * 我的收藏
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /my_collection 02.我的收藏
	 * @apiGroup MyExhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {string} api_token token
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {int} skip 数据偏移量默认0
	 * @apiParam {int} take 查询数量默认10
	 * @apiSuccess {string} date 日期
	 * @apiSuccess {array} list 展品列表
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} exhibit_img 图片url
	 * @apiSuccess {string} exhibit_id 展品id
	 * @apiSuccess {string} datetime 收藏时间
	 */
	public function my_collection()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'skip' => 'required|man:0|integer',
			'take' => 'required|man:0|integer',
		]);
		$uid = Auth::user()->uid;
		$language = request('language', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$infolist = ExhibitLike::where('exhibit_like.uid', $uid)->join('exhibit', 'exhibit.id', '=', 'exhibit_like.exhibit_id')->join('exhibit_language', function ($join) use ($language) {
			$join->on('exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', '=', $language);
		})->where('exhibit_like.type', 2)->skip($skip)->take($take)->select('exhibit_like.created_at as datetime', 'exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id')->orderBy('exhibit_like.id', 'desc')->get();
		$data = [];
		foreach ($infolist as $k => $g) {
			$imgs = json_decode($g->exhibit_img, true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$data[$k]['exhibit_name'] = $g->exhibit_name;
			$data[$k]['exhibit_id'] = $g->exhibit_id;
			$data[$k]['exhibit_img'] = get_file_url($imgs);
			$data[$k]['datetime'] = date('Y.m.d H:i', strtotime($g->datetime));
		}
		return response_json(1, $data);
	}

	/**
	 * 我的评论
	 *
	 * @author yyj 20180321
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /my_comment 03.我的评论
	 * @apiGroup MyExhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {string} api_token token
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {int} skip 数据偏移量默认0
	 * @apiParam {int} take 查询数量默认10
	 * @apiSuccess {json} data 结果详情
	 * @apiSuccess {string} exhibit_name 展品名称
	 * @apiSuccess {string} exhibit_img 图片url
	 * @apiSuccess {int} exhibit_id 展品id
	 * @apiSuccess {string} datetime 评论时间
	 * @apiSuccess {int} comment_id 评论id
	 * @apiSuccess {string} comment 评论内容
	 * @apiSuccess {int} is_check 是否通过审核1审核中，2已通过,3未通过
	 */
	public function my_comment()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'skip' => 'required|man:0|integer',
			'take' => 'required|man:0|integer',
		]);
		$uid = Auth::user()->uid;
		$language = request('language', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$info = ExhibitComment::where('exhibit_comment.type', 2)->join('exhibit', 'exhibit.id', '=', 'exhibit_comment.exhibit_id')->join('exhibit_language', function ($join) use ($language) {
			$join->on('exhibit_language.exhibit_id', '=', 'exhibit.id')->where('exhibit_language.language', '=', $language);
		})->where('exhibit_comment.uid', $uid)->skip($skip)->take($take)->select('exhibit_comment.created_at as datetime', 'exhibit_language.exhibit_name', 'exhibit.exhibit_img', 'exhibit.id as exhibit_id', 'exhibit_comment.id as comment_id', 'exhibit_comment.comment', 'exhibit_comment.is_check')->get();
		$data = [];
		foreach ($info as $k => $g) {
			$imgs = json_decode($g->exhibit_img, true);
			$imgs = isset($imgs['exhibit_list']) ? $imgs['exhibit_list'] : '';
			$data[$k]['exhibit_name'] = $g->exhibit_name;
			$data[$k]['exhibit_id'] = $g->exhibit_id;
			$data[$k]['exhibit_img'] = get_file_url($imgs);
			$data[$k]['datetime'] = date('m-d H:i', strtotime($g->datetime));
			$data[$k]['is_check'] = $g->is_check;
			$data[$k]['comment'] = $g->comment;
			$data[$k]['comment_id'] = $g->comment_id;
		}
		return response_json(1, $data);
	}

	/**
	 * 我的评论删除接口
	 *
	 * @author yyj 20171111
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /del_my_comment 04.我的评论删除接口
	 * @apiGroup MyExhibit
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓,w:微信
	 * @apiParam {int} comment_id 评论编号
	 * @apiParam {string} api_token token
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function del_my_comment()
	{
		$this->validate([
			'comment_id' => 'required|min:0|integer',
		]);
		$uid = Auth::user()->uid;
		$comment_id = request('comment_id', 0);
		$r = ExhibitComment::where('uid', $uid)->where('id', $comment_id)->delete();
		if ($r) {
			ExhibitCommentLikelist::where('comment_id', $comment_id)->delete();
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}
}