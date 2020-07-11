<?php

namespace App\Http\Controllers\Api;

use App\Dao\UploadedFileDao;
use App\Models\ExUserVisit;
use App\Models\Pai;
use App\Exceptions\ApiErrorException;
use App\Models\PaiComment;
use App\Models\PaiLike;
use Illuminate\Support\Facades\Auth;

class PaiController extends Controller
{
	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 随手拍图片上传
	 *
	 * @author yyj 20171113
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /pai_uploadimg 01.随手拍图片上传
	 * @apiGroup  Pai
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户token
	 * @apiParam {file} pai 随手拍图片最大上传5M,app上传前压缩处理一下
	 * @apiSuccess {string} data 图片地址
	 * @apiSuccessExample {json} 返回值
	 * {"status":1,"data":"\u65b0\u6635\u79f0","msg":""}
	 */
	public function pai_uploadimg()
	{
		$this->validate([
			'pai' => 'required|file'
		]);
		$uid = Auth::user()->uid;

		// 保存图片
		$file = UploadedFileDao::saveFile('pai', 'FT_PAI', $uid);
		if (!$file['status']) {
			throw new ApiErrorException($file['data']);
		}
		/*//用户上传头像次数+1
		$u_ex_info=ExUserVisit::where('uid',$uid)->first();
		if(empty($u_ex_info)){
			ExUserVisit::create(['uid'=>$uid,'use_time'=>0,'listen_num'=>0,'photo_num'=>1]);
		}
		else{
			ExUserVisit::where('uid',$uid)->increment('photo_num');
		}*/

		return response_json(1, $file['data']->file_path . '/' . $file['data']->file_name);
	}

	/**
	 * 微信图片上传
	 *
	 * @author yyj 20170422
	 *
	 * @api {POST} /wx_upload_img 09.微信图片上传
	 * @apiGroup Pai
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} img_url 微信图片存储网址
	 * @apiParam {string} api_token 用户token
	 * @apiSuccess {string} data 图片地址
	 */
	public function wx_upload_img()
	{
		$this->validate([
			'img_url' => 'required'
		]);
		$img_url = request('img_url');
		$save_path = public_path() . '/uploadfiles/wx_pai/' . date('Ymd', time()) . '/';
		$savename = ltrim(strrchr($img_url, '='), '=') . '.jpg';
		$filepath = $save_path . $savename;
		if (empty($img_url) || $savename == '.jpg') {
			return response_json(0, [], '请填写图片网址');
		}
		if (!file_exists($save_path)) {
			mkdir($save_path, 0777, true);
		}
		$type = 0;//0浏览器自动弹窗下载  1远程图片链接下载
		if ($type) {
			$ch = curl_init();
			$timeout = 5;
			curl_setopt($ch, CURLOPT_URL, $img_url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
			$img = curl_exec($ch);
			curl_close($ch);
		} else {
			ob_start();
			readfile($img_url);
			$img = ob_get_contents();
			ob_end_clean();
		}
		//$size=strlen($img);
		//文件大小
		$fp2 = fopen($filepath, 'a');
		fwrite($fp2, $img);
		fclose($fp2);
		if (file_exists($filepath)) {
			//用户上传头像次数+1
			/*$uid = Auth::user()->uid;
			$u_ex_info=ExUserVisit::where('uid',$uid)->first();
			if(empty($u_ex_info)){
				ExUserVisit::create(['uid'=>$uid,'use_time'=>0,'listen_num'=>0,'photo_num'=>1]);
			}
			else{
				ExUserVisit::where('uid',$uid)->increment('photo_num');
			}*/
			return response_json(1, '/uploadfiles/wx_pai/' . date('Ymd', time()) . '/' . $savename, '图片下载成功');
		} else {
			return response_json(0, '图片下载失败');
		}
	}

	/**
	 * 随手拍发布
	 *
	 * @author yyj 20171113
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 *
	 * @api {POST} /send_pai 02.随手拍发布
	 * @apiGroup  Pai
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web，t：触屏或手机
	 * @apiParam {string} api_token 用户token
	 * @apiParam {string} pai_content 内容最多输入500个字符
	 * @apiParam {string} pai_img[] 调用图片上传接口后返回的图片地址 可提交多个 最多上传九张
	 * @apiSuccess {int} data 操作结果1成功0失败
	 *
	 */
	public function send_pai()
	{
		$this->validate([
			'pai_content' => 'nullable|max:500|string',
		]);
		$uid = Auth::user()->uid;
		$content = request('pai_content');
		// 处理图片
		$img = request('pai_img');
		$imgs = [];
		$allowedExts = array(
			"gif",
			"jpeg",
			"jpg",
			"png"
		);
		if (is_array($imgs)) {
			foreach ($img as $k => $v) {
				if (!empty($v)) {
					$temp = explode(".", $v);
					$extension = end($temp);
					if (in_array($extension, $allowedExts) && (strstr($v, '/uploadfiles/pai/') !== false || strstr($v, '/uploadfiles/wx_pai/') !== false)) {
						array_push($imgs, $v);
					} else {
						return response_json(0, [], '图片格式错误');
					}
				}
			}
		}
		$num = count($imgs);
		if ($num > 9) {
			throw new ApiErrorException('最多上传9张图片');
		}
		$imgs = json_encode($imgs);
		if (empty($content) && empty($num)) {
			throw new ApiErrorException('文字和图片至少要提交一项');
		}
		if (config('app_check')['pai']) {
			$is_check = 1;
		} else {
			$is_check = 2;
		}
		$r = Pai::create([
			'uid' => $uid,
			'content' => $content,
			'imgs' => $imgs,
			'is_check' => $is_check
		]);
		if ($r) {
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}

	/**
	 * 随手拍列表
	 *
	 * @author yyj 20171113
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /pai_list 03.随手拍列表
	 * @apiGroup Pai
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {int} skip 数据偏移量
	 * @apiParam {int} take 查询数量
	 * @apiParam {string} [api_token] 用户token登录后判断是否点赞
	 * @apiSuccess {array} data 数据详情
	 * @apiSuccess {int} pid 编号
	 * @apiSuccess {string} content 内容
	 * @apiSuccess {string} addtime 发布时间
	 * @apiSuccess {int} like_num 点赞数
	 * @apiSuccess {int} comment_num 评论数
	 * @apiSuccess {string} user_nicename 用户昵称
	 * @apiSuccess {string} avatar 头像
	 * @apiSuccess {array} imgs 发布的图片
	 * @apiSuccess {int} is_like 是否点赞 0未点赞 1已点赞
	 */
	public function pai_list()
	{
		$this->validate([
			'skip' => 'required|min:0|integer',
			'take' => 'required|min:0|integer',
		]);
		$skip = request('skip', 0);
		$take = request('take', 10);
		// 取得当前用户的点赞数据
		$ulikes = [];
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
			$ulikes = PaiLike::where('type', 1)->where('uid', $uid)->pluck('pai_id');
			$ulikes = empty($ulikes) ? [] : $ulikes->toArray();
		}
		$data = Pai::join('users', 'users.uid', '=', 'pai.uid');
		$data = $data->where('pai.is_check', 2);
		$data = $data->select('pai.id as pid', 'pai.content', 'pai.created_at as addtime', 'pai.like_num', 'pai.comment_num', 'pai.imgs', 'users.avatar', 'users.nickname')->orderBy('pai.id', 'desc')->skip($skip)->take($take)->get();
		foreach ($data as $k => $v) {
			$data[$k]->imgs = json_decode($v->imgs, true);
			//判断是否点赞
			if (in_array($v['pid'], $ulikes)) {
				$data[$k]->is_like = 1;
			} else {
				$data[$k]->is_like = 0;
			}
			$data[$k]->comment_num = PaiComment::where('pai_id', $v->pid)->where('is_check', 2)->count();
		}
		return response_json(1, $data);
	}

	/**
	 * 随手拍评论
	 *
	 * @author yyj 20171113
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {POST} /pai_comment 04.随手拍评论
	 * @apiGroup Pai
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} pai_comment 评论内容最多200字
	 * @apiParam {int} pid 随手拍id
	 * @apiParam {string} api_token 用户token
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function pai_comment()
	{
		$this->validate([
			'pai_comment' => 'required|max:200|string',
			'pid' => 'required|min:1|integer',
		]);
		$uid = Auth::user()->uid;
		$comment = request('pai_comment');
		$pid = request('pid');
		if (config('app_check')['pai_comment']) {
			$is_check = 1;
		} else {
			$is_check = 2;
		}
		$r = PaiComment::create([
			'pai_id' => $pid,
			'uid' => $uid,
			'comment' => $comment,
			'is_check' => $is_check
		]);

		if ($r) {
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}

	/**
	 * 5随手拍评论点赞取消
	 *
	 * @author yyj 20171113
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /pai_dolike 05.随手拍评论点赞取消（系统自动判断执行点赞还是取消）
	 * @apiGroup Pai
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} api_token 用户token
	 * @apiParam {int} type 1随手拍点赞2随手拍评论点赞
	 * @apiParam {int} [pid] 随手拍id
	 * @apiParam {int} [comment_id] 随手拍评论id
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */

	public function pai_dolike()
	{
		$this->validate([
			'type' => 'required|min:1|max:2|integer',
		]);
		$uid = Auth::user()->uid;
		$type = request('type', 1);
		if ($type == 1) {
			$this->validate([
				'pid' => 'required|min:1|integer',
			]);
			$pai_id = request('pid', 0);
			// 检查是否已经点过赞
			$is_like = PaiLike::where('uid', $uid)->where('type', $type)->where('pai_id', $pai_id)->count();
			if ($is_like) {
				$r = PaiLike::where('uid', $uid)->where('type', $type)->where('pai_id', $pai_id)->delete();
				Pai::where('id', $pai_id)->decrement('like_num');
			} else {
				$r = PaiLike::create([
					'type' => $type,
					'uid' => $uid,
					'pai_id' => $pai_id,
					'pai_comment_id' => 0,
				]);
				Pai::where('id', $pai_id)->increment('like_num');
			}
		} else {
			$this->validate([
				'comment_id' => 'required|min:1|integer',
			]);
			$pai_comment_id = request('comment_id', 0);
			// 检查是否已经点过赞
			$is_like = PaiLike::where('uid', $uid)->where('type', $type)->where('pai_comment_id', $pai_comment_id)->count();
			if ($is_like) {
				$r = PaiLike::where('uid', $uid)->where('type', $type)->where('pai_comment_id', $pai_comment_id)->delete();
				PaiComment::where('id', $pai_comment_id)->decrement('like_num');
			} else {
				$r = PaiLike::create([
					'type' => $type,
					'uid' => $uid,
					'pai_id' => 0,
					'pai_comment_id' => $pai_comment_id,
				]);
				PaiComment::where('id', $pai_comment_id)->increment('like_num');
			}
		}
		if ($r) {
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}

	/**
	 * 6随手拍评论列表
	 *
	 * @author yyj 20171113
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /pai_comment_list 06.随手拍评论列表
	 * @apiGroup Pai
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {int} pid 随手拍id
	 * @apiParam {int} skip 数据偏移量
	 * @apiParam {int} take 查询数量
	 * @apiParam {string} [api_token] 用户token登录后上传
	 * @apiSuccess {array} data 评论列表
	 * @apiSuccess {int} comment_id 评论编号
	 * @apiSuccess {int} datetime 评论时间
	 * @apiSuccess {string} nickname 用户昵称
	 * @apiSuccess {string} avatar 头像
	 * @apiSuccess {string} is_like 是否点赞
	 * @apiSuccess {string} comment 评论内容
	 * @apiSuccess {string} like_num 点赞数
	 */
	public function pai_comment_list()
	{
		$this->validate([
			'pid' => 'required|min:1|integer',
			'skip' => 'required|min:0|integer',
			'take' => 'required|min:0|integer',
		]);

		$pid = request('pid', 1);
		$skip = request('skip', 0);
		$take = request('take', 10);

		$data = PaiComment::join('users', 'users.uid', '=', 'pai_comment.uid')->where('pai_comment.pai_id', $pid);
		$data = $data->where('pai_comment.is_check', 2);
		$data = $data->select('pai_comment.comment', 'pai_comment.created_at as datetime', 'pai_comment.like_num', 'users.nickname', 'users.avatar', 'pai_comment.id as comment_id')->skip($skip)->take($take)->orderBy('pai_comment.created_at', 'desc')->get();
		// 取得当前用户的点赞数据
		$ulikes = [];
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
			$ulikes = PaiLike::where('type', 2)->where('uid', $uid)->pluck('pai_comment_id');
			$ulikes = empty($ulikes) ? [] : $ulikes->toArray();
		}

		foreach ($data as $k => $v) {
			//判断是否点赞
			if (in_array($v['comment_id'], $ulikes)) {
				$data[$k]->is_like = 1;
			} else {
				$data[$k]->is_like = 0;
			}
			$data[$k]['datetime'] = date('m-d H:i', strtotime($v['datetime']));
		}

		return response_json(1, $data);
	}

	/**
	 * 我的随手怕列表
	 *
	 * @author yyj 20171113
	 * @return \Illuminate\Http\JsonResponse
	 *
	 * @api {GET} /my_pai_list 07.我的随手怕列表
	 * @apiGroup Pai
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {int} skip 数据偏移量
	 * @apiParam {int} take 查询数量
	 * @apiParam {string} api_token 用户token登录后判断是否点赞
	 * @apiSuccess {array} data 数据详情
	 * @apiSuccess {int} pid 编号
	 * @apiSuccess {string} content 内容
	 * @apiSuccess {string} addtime 发布时间
	 * @apiSuccess {int} like_num 点赞数
	 * @apiSuccess {int} comment_num 评论数
	 * @apiSuccess {array} imgs 发布的图片
	 * @apiSuccess {int} is_like 是否点赞 0未点赞 1已点赞
	 * @apiSuccess {int} is_check 是否通过审核 1未通过 2已通过
	 * @apiSuccess {string} user_nicename 用户昵称
	 * @apiSuccess {string} avatar 头像
	 */
	public function my_pai_list()
	{
		$this->validate([
			'skip' => 'required|min:0|integer',
			'take' => 'required|min:0|integer',
		]);
		$skip = request('skip', 0);
		$take = request('take', 10);
		// 取得当前用户的点赞数据
		$uid = Auth::user()->uid;
		$ulikes = PaiLike::where('type', 1)->where('uid', $uid)->pluck('pai_id');
		$ulikes = empty($ulikes) ? [] : $ulikes->toArray();
		$data = Pai::where('uid', $uid)->select('id as pid', 'content', 'created_at as addtime', 'like_num', 'comment_num', 'imgs', 'is_check')->orderBy('id', 'desc')->skip($skip)->take($take)->get();
		foreach ($data as $k => $v) {
			$data[$k]->imgs = json_decode($v->imgs, true);
			//判断是否点赞
			if (in_array($v['pid'], $ulikes)) {
				$data[$k]->is_like = 1;
			} else {
				$data[$k]->is_like = 0;
			}
			$data[$k]->comment_num = PaiComment::where('pai_id', $v->pid)->where('is_check', 2)->count();
			$data[$k]->avatar = Auth::user()->avatar;
			$data[$k]->nickname = Auth::user()->nickname;
		}
		return response_json(1, $data);
	}

	/**
	 * 8我的随手怕删除接口
	 *
	 * @author yyj 20171113
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /del_my_pai_list 08.我的随手怕删除接口
	 * @apiGroup Pai
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：微信
	 * @apiParam {string} api_token 用户token
	 * @apiParam {int} pid 随手拍id
	 * @apiSuccess {int} data 操作结果1成功0失败
	 */
	public function del_my_pai_list()
	{
		$this->validate([
			'pid' => 'required|min:1|integer',
		]);
		$uid = Auth::user()->uid;
		$pid = request('pid', 0);
		$r = Pai::where('id', $pid)->where('uid', $uid)->delete();
		if ($r) {
			PaiLike::where('type', 1)->where('pai_id', $pid)->delete();
			PaiComment::where('pai_id', $pid)->delete();
			return response_json(1, 1);
		} else {
			return response_json(1, 0);
		}
	}
}
