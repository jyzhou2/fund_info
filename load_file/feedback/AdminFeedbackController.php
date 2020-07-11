<?php

namespace App\Http\Controllers\Admin\Feedback;

use App\Http\Controllers\Admin\BaseAdminController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Feedback;

/**
 * 意见反馈控制器
 *
 * @author ljy
 * @package App\Http\Controllers\Data
 */
class FeedbackController extends BaseAdminController
{

	//是否显示意见反馈功能
	private $is_show_reply = true;

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 意见反馈
	 *
	 * @author yyj 20180717
	 */
	public function index()
	{
		// 处理排序
		$sort = request('sort');
		$order = request('order');
		if ($sort && $order) {
			$query = Feedback::orderBy($sort, $order);
		} else {
			$query = Feedback::orderBy('id', 'desc');
		}
		$reply_status = request('reply_status', 0);
		if ($reply_status == 1) {
			$query->where('reply_uid', '=', 0);
		} elseif ($reply_status == 2) {
			$query->where('reply_uid', '<>', 0);
		}
		$username = request('username');
		if ($username) {
			$query->where('feedback_username', 'LIKE', "%" . $username . "%");
		}
		$phone = request('phone');
		if ($phone) {
			$query->where('feedback_user_phone', $phone);
		}
		// 筛选发表时间时间
		if (request('created_at')) {
			list($begin, $end) = explode(' ~ ', request('created_at'));
			$query->whereBetween('feedback_date_time', [
				date('Y-m-d H:i:s', strtotime($begin)),
				date('Y-m-d H:i:s', strtotime('+1 day', strtotime($end)))
			]);
		}

		// 取得列表
		$data = $query->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$data->appends(app('request')->all());
		return view('admin.feedback.index', [
			'data' => $data,
			'is_show_reply' => $this->is_show_reply
		]);

	}

	/*
	 * 意见回复页面调取
	 *
	 * @author yyj 20180717
	 */
	public function reply()
	{
		$id = request('id');
		$reply_uid = Auth::user()->uid;
		$reply_username = Auth::user()->username;
		return view('admin.feedback.reply', [
			'id' => $id,
			'reply_username' => $reply_username,
			'reply_uid' => $reply_uid
		]);
	}

	/*
	 * 意见回复提交
	 *
	 * @author yyj 20180717
	 */
	public function reply_save()
	{
		$id = request('id');
		$data['reply_uid'] = request('reply_uid');
		$data['reply_username'] = request('reply_username');
		$data['reply_content'] = request('reply_content');
		$data['reply_datetime'] = date("Y-m-d H:i:s", time());
		//更改为未读状态
		$data['is_read'] = 0;
		$r = Feedback::where('id', $id)->update($data);
		if ($r) {
			$success['status'] = 'true';
			$success['msg'] = '回复成功';
			return $success;
		}
	}

	/**
	 * 删除
	 *
	 * @author yyj 20180717
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function delete($ids)
	{
		if (request()->ajax()) {
			$idArray = explode(',', $ids);
			Feedback::destroy($idArray);
			return $this->success('', 's_del');
		}
	}

}