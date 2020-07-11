<?php

namespace App\Http\Controllers\Admin\PaiCheck;

use App\Models\ExhibitComment;
use App\Models\Pai;
use App\Models\PaiComment;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\BaseAdminController;

class PaiCheckController extends BaseAdminController
{
	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 随手拍审核
	 *
	 * @author yyj 20171026
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function pai_list()
	{
		$is_check = request('is_check', 1);
		// 处理排序
		$query = Pai::orderBy('pai.id', 'desc');
		// 筛选是评论内容
		if (request('comment')) {
			$query->where('pai.content', 'LIKE', "%" . request('comment') . "%");
		}
		// 筛选是否审核
		if ($is_check) {
			$query->where('pai.is_check', $is_check);
		}
		// 筛选发表时间时间
		if (request('created_at')) {
			list($begin, $end) = explode(' ~ ', request('created_at'));
			$query->whereBetween('pai.created_at', [
				date('Y-m-d H:i:s', strtotime($begin)),
				date('Y-m-d H:i:s', strtotime('+1 day', strtotime($end)))
			]);
		}
		// 取得列表
		$info = $query->leftJoin('users', 'users.uid', '=', 'pai.uid')->select('pai.*', 'users.username', 'users.nickname')->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());
		return view('admin.paicheck.pai_list', [
			'info' => $info,
		]);
	}

	/**
	 * 随手拍评论审核
	 *
	 * @author yyj 20171026
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function pai_comment_list()
	{
		$is_check = request('is_check', 1);
		// 处理排序
		$query = PaiComment::orderBy('pai_comment.id', 'desc');
		// 筛选是评论内容
		if (request('comment')) {
			$query->where('pai_comment.comment', 'LIKE', "%" . request('comment') . "%");
		}
		// 筛选是否审核
		if ($is_check) {
			$query->where('pai_comment.is_check', $is_check);
		}
		// 筛选发表时间时间
		if (request('created_at')) {
			list($begin, $end) = explode(' ~ ', request('created_at'));
			$query->whereBetween('pai.created_at', [
				date('Y-m-d H:i:s', strtotime($begin)),
				date('Y-m-d H:i:s', strtotime('+1 day', strtotime($end)))
			]);
		}
		// 取得列表
		$info = $query->leftJoin('users', 'users.uid', '=', 'pai_comment.uid')->select('pai_comment.*', 'users.username', 'users.nickname')->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$info->appends(app('request')->all());
		return view('admin.paicheck.pai_comment_list', [
			'info' => $info,
		]);
	}

	/**
	 * 通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function pass_check($type, $ids)
	{
		if (request()->ajax()) {
			$idArray = explode(',', $ids);
			if (!is_array($idArray)) {
				$idArray[] = $idArray;
			}
			if ($type == 3) {
				Pai::whereIn('id', $idArray)->update(['is_check' => 2]);
				return $this->success(get_session_url('pai_list'));
			} elseif ($type == 4) {
				PaiComment::whereIn('id', $idArray)->update(['is_check' => 2]);
				return $this->success(get_session_url('pai_comment_list'));
			}
		}
	}

	/**
	 * 不通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function unpass_check($type, $ids)
	{
		if (request()->ajax()) {
			$idArray = explode(',', $ids);
			if (!is_array($idArray)) {
				$idArray[] = $idArray;
			}
			if ($type == 3) {
				Pai::whereIn('id', $idArray)->update(['is_check' => 3]);
				return $this->success(get_session_url('pai_list'));
			} elseif ($type == 4) {
				PaiComment::whereIn('id', $idArray)->update(['is_check' => 3]);
				return $this->success(get_session_url('pai_comment_list'));
			}
		}
	}

	/**
	 * 删除
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function del_check($type, $ids)
	{
		if (request()->ajax()) {
			$idArray = explode(',', $ids);
			if (!is_array($idArray)) {
				$idArray[] = $idArray;
			}
			if ($type == 3) {
				Pai::whereIn('id', $idArray)->delete();
				return $this->success(get_session_url('pai_list'));
			} elseif ($type == 4) {
				PaiComment::whereIn('id', $idArray)->delete();
				return $this->success(get_session_url('pai_comment_list'));
			}
		}
	}
}
