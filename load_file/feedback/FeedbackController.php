<?php

namespace App\Http\Controllers\Api;

use App\Models\Feedback;
use Illuminate\Support\Facades\Auth;
use App\Dao\UploadedFileDao;
use App\Exceptions\ApiErrorException;

class FeedbackController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 意见反馈
	 *
	 * @apiDescription mxf 20171110
	 * @return \Illuminate\Http\JsonResponse
	 * @api {POST} /feedback/feedback_save 1意见反馈
	 * @apiGroup Feedback
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {string} [api_token] 用户token
	 * @apiParam {string} [img] 反馈图片地址
	 * @apiParam {string} content 反馈内容
	 * @apiParam {string} [phone] 手机号
	 */
	public function feedback_save()
	{

		$this->validate([
			'content' => 'required'
		]);
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
			$uname = $user->username;
		} else {
			$uid = 0;
			$uname = "匿名用户";
		}

		$data['feedback_uid'] = $uid;
		$data['img'] = request('img');
		$data['feedback_username'] = $uname;
		$data['feedback_content'] = request('content');
		$data['feedback_user_phone'] = request('phone');
		$data['feedback_date_time'] = date("Y-m-d H:i:s", time());
		$data['is_read'] = 1;
		Feedback::create($data);
		return response_json(1, [], "");
	}

	/**
	 * 意见反馈图片
	 *
	 * @apiDescription mxf 20171110
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {POST} /feedback/feedback_img 2意见反馈图片上传
	 * @apiGroup Feedback
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {string} [api_token] 用户签名
	 * @apiParam {file} feedback 图片
	 * @apiSuccess {string} data 图片地址
	 */
	public function feedback_img()
	{
		$this->validate([
			'feedback' => 'required|file'
		]);
		$user = Auth::user();
		if (false == empty($user)) {
			$uid = $user->uid;
		} else {
			$uid = 0;
		}
		// 保存图片
		$file = UploadedFileDao::saveFile('feedback', 'FT_FEEDBACK', $uid);
		if (!$file['status']) {
			throw new ApiErrorException($file['data']);
		}

		$files = $file['data']->file_path . '/' . $file['data']->file_name;
		return response_json(1, $files, trans("msg.s_operate"));
	}

	/**
	 * 我的反馈
	 *
	 * @apiDescription mxf 20171110
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {GET} /feedback/my_feedback 3我的反馈
	 * @apiGroup Feedback
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {string} api_token 用户签名
	 * @apiParam {int} skip 数据偏移量默认0
	 * @apiParam {int} take 查询数量默认10
	 * @apiSuccess {string} data 反馈详情
	 * @apiSuccess {int} feedback_id 反馈id
	 * @apiSuccess {string} feedback_content 反馈内容
	 * @apiSuccess {string} img 反馈图片
	 * @apiSuccess {string} feedback_date_time 反馈时间
	 * @apiSuccess {int} is_read 是否查看回复0未查看 1已查看
	 * @apiSuccess {int} reply_uid 是否回复0未回复，大于0已回复
	 * @apiSuccess {string} reply_content 回复内容
	 * @apiSuccess {string} reply_datetime 回复时间
	 */
	public function my_feedback()
	{
		$this->validate([
			'language' => 'required|min:0|integer',
			'skip' => 'required|min:0|integer',
			'take' => 'required|min:0|integer',
		]);
		$skip = request('skip', 0);
		$take = request('take', 10);
		$user = Auth::user();
		$uid = $user->uid;
		$info = Feedback::where('feedback_uid', $uid)->select('id as feedback_id', 'feedback_content', 'img', 'feedback_date_time', 'is_read', 'reply_uid', 'reply_content', 'reply_datetime')->orderBy('id', 'desc')->skip($skip)->take($take)->get()->toArray();
		return response_json(1, $info);
	}

	/**
	 * 我的反馈已读
	 *
	 * @apiDescription mxf 20171110
	 * @return \Illuminate\Http\JsonResponse
	 * @throws ApiErrorException
	 * @api {POST} /feedback/read_my_feedback 4我的反馈已读
	 * @apiGroup Feedback
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓，w：Web
	 * @apiParam {int} language 语种，1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语
	 * @apiParam {string} api_token 用户签名
	 * @apiParam {int} feedback_id 反馈id
	 */
	public function read_my_feedback()
	{
		$this->validate([
			'feedback_id' => 'required|min:1|integer',
		]);
		$feedback_id = request('feedback_id');
		$user = Auth::user();
		$uid = $user->uid;
		$r = Feedback::where('feedback_uid', $uid)->where('id', $feedback_id)->update(['is_read' => 1]);
		if ($r) {
			return response_json(1, [], '操作成功');
		} else {
			return response_json(0, [], '操作失败，请重试');
		}

	}
}
