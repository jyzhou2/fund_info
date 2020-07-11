<?php

namespace App\Dao;

use App\Models\Exhibit;
use App\Models\ExhibitComment;
use App\Models\ExhibitCommentLikelist;
use App\Models\Exhibition;
use App\Models\ExhibitLike;
use Illuminate\Support\Facades\DB;

/**
 * 展品列表距离排序
 *
 * @author yyj 20171111
 */
class ExhibitDao extends Exhibit
{
	/**
	 * 评论列表
	 *
	 * @author yyj 20171112
	 * @param int $type 1展厅评论2展品评论
	 * @param int $skip
	 * @param int $take
	 * @param int $ex_id 展厅展品编号
	 * @param int $uid
	 * @return array
	 */
	public static function comment_list($type, $skip, $take, $ex_id, $uid)
	{
		$comment_list = ExhibitComment::join('users', 'users.uid', '=', 'exhibit_comment.uid')->where('exhibit_comment.type', $type);
		if (config('app_check')['exhibit_comment']) {
			$comment_list = $comment_list->where('exhibit_comment.is_check', 2);
		}
		if ($type == 1) {
			$comment_list = $comment_list->where('exhibit_comment.exhibition_id', $ex_id);
		} else {
			$comment_list = $comment_list->where('exhibit_comment.exhibit_id', $ex_id);
		}
		$comment_list = $comment_list->select('exhibit_comment.comment', 'exhibit_comment.created_at', 'exhibit_comment.like_num', 'users.nickname', 'users.avatar', 'exhibit_comment.id')->skip($skip)->take($take)->orderBy('exhibit_comment.created_at', 'desc')->get();
		$list = [];
		if (!empty($comment_list)) {
			foreach ($comment_list->toArray() as $k => $g) {
				$list[$k]['comment_id'] = $g['id'];
				$list[$k]['comment'] = $g['comment'];
				$list[$k]['datetime'] = date('m-d H:i', strtotime($g['created_at']));
				$list[$k]['like_num'] = $g['like_num'];
				$list[$k]['nickname'] = $g['nickname'];
				$list[$k]['avatar'] = $g['avatar'];
				if ($uid) {
					$list[$k]['is_like'] = ExhibitCommentLikelist::where('uid', $uid)->where('comment_id', $list[$k]['comment_id'])->count();
				} else {
					$list[$k]['is_like'] = 0;
				}
			}
		}
		return $list;
	}

	/**
	 * 蓝牙关联列表
	 *
	 * @author yyj 20171112
	 * @param array $auto_list 蓝牙关联列表
	 * @return array
	 */
	public static function autonum_list($auto_list)
	{

		$data = [];
		//获取展品详情
		$exhibit_list = Exhibit::where('is_show_map', 1)->select('id as exhibit_id', 'exhibit_name', 'exhibition_id')->get();
		//获取展厅列表
		$exhibition = Exhibition::select('exhibition_name', 'floor_id', 'id as exhibition_id')->get();
		$is_add = 1;
		if (!empty($exhibition)) {
			foreach ($exhibition as $k => $g) {
				$data[$g->exhibition_id] = [
					'exhibition_name' => $g->exhibition_name,
					'exhibition_id' => $g->exhibition_id,
					'exhibit_list' => [],
					'check_num' => 0,
				];
			}
		}
		foreach ($exhibit_list as $k => $g) {
			$exhibit_info = [
				'exhibiti_name' => $g->exhibit_name,
				'exhibit_id' => $g->exhibit_id,
				'is_check' => in_array($g->exhibit_id, $auto_list) ? 1 : 0,
			];
			$data[$g->exhibition_id]['exhibit_list'][] = $exhibit_info;
			if ($exhibit_info['is_check'] == 1) {
				$data[$g->exhibition_id]['check_num'] += 1;
				$is_add = 2;
			}
		}
		sort($data);
		$re_data['data'] = $data;
		$re_data['is_add'] = $is_add;
		return $re_data;
	}

	/**
	 * 通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 */
	public static function pass_check($type, $ids)
	{
		$idArray = explode(',', $ids);
		if (!is_array($idArray)) {
			$idArray[] = $idArray;
		}
		if ($type == 1) {
			ExhibitComment::whereIn('id', $idArray)->where('type', 1)->update(['is_check' => 2]);
		} elseif ($type == 2) {
			ExhibitComment::whereIn('id', $idArray)->where('type', 2)->update(['is_check' => 2]);
		}
	}

	/**
	 * 不通过审核
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 */
	public static function unpass_check($type, $ids)
	{
		$idArray = explode(',', $ids);
		if (!is_array($idArray)) {
			$idArray[] = $idArray;
		}
		if ($type == 1) {
			ExhibitComment::whereIn('id', $idArray)->where('type', 1)->update(['is_check' => 3]);
		} elseif ($type == 2) {
			ExhibitComment::whereIn('id', $idArray)->where('type', 2)->update(['is_check' => 3]);
		}
	}

	/**
	 * 删除评论
	 *
	 * @author yyj 20171115
	 * @param int $type
	 * @param string $ids
	 */
	public static function del_check($type, $ids)
	{
		$idArray = explode(',', $ids);
		if (!is_array($idArray)) {
			$idArray[] = $idArray;
		}
		if ($type == 1) {
			ExhibitComment::whereIn('id', $idArray)->where('type', 1)->delete();
		} elseif ($type == 2) {
			ExhibitComment::whereIn('id', $idArray)->where('type', 2)->delete();
		}
	}
}
