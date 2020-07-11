<?php

namespace App\Http\Controllers\Admin\Article;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Comment;

/**
 * 评论控制器
 *
 * @package App\Http\Controllers
 */
class CommentController extends BaseAdminController
{

	/**
	 * 评论列表
	 *
	 * @author lxp 20170304
	 * @param int $status 状态，1正常，2待审核
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
	 */
	public function index($status = 1)
	{
		// 处理排序
		$sort = request('sort', 'comment_id');
		$order = request('order', 'desc');

		$query = Comment::orderBy($sort, $order);
		$query->where('status', $status);
		// 筛选内容
		if (request('comment')) {
			$query->where('comment', 'LIKE', "%" . request('comment') . "%");
		}
		// 筛选发表时间
		if (request('add_time')) {
			list($begin, $end) = explode(' - ', request('add_time'));
			$query->whereBetween('add_time', [
				date('Y-m-d H:i:s', strtotime($begin)),
				date('Y-m-d H:i:s', strtotime('+1 day', strtotime($end)))
			]);
		}
		// 取得列表
		$comments = $query->article()->paginate(parent::$perpage);
		// 将查询参数拼接到分页链接中
		$comments->appends(request()->all());

		return view('admin.article.comment', [
			'comments' => $comments,
			'status' => $status
		]);
	}

	/**
	 * 删除评论
	 *
	 * @author lxp 20170304
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function delete($ids)
	{
		if (request()->ajax()) {
			$idArray = explode(',', $ids);

			// 删除评论
			Comment::destroy($idArray);

			return $this->success('', 's_del');
		}
	}

	/**
	 * 审核评论
	 *
	 * @author lxp 20170304
	 * @param string $ids
	 * @return \Illuminate\Contracts\View\Factory|\Illuminate\Http\JsonResponse|\Illuminate\View\View
	 */
	public function pass($ids)
	{
		if (request()->ajax()) {
			$idArray = explode(',', $ids);
			// 判断是否允许修改
			if (!empty($idArray)) {
				Comment::whereIn('comment_id', $idArray)->update([
					'status' => 1
				]);

				return $this->success();
			}
		}
	}
}
