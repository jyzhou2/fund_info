<?php

namespace App\Http\Controllers\Api;

use App\Models\Queslist;
use App\Models\QuesinfoList;
use App\Models\QuesinfoOption;
use App\Models\QuesTextinfo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{

	public function __construct()
	{
		parent::_init();
	}

	/**
	 * 问卷调查
	 *
	 * @apiDescription yyj 20180706
	 * @return \Illuminate\Http\JsonResponse
	 * @api {GET} /question/question_list 01.问卷调查列表
	 * @apiGroup Question
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓
	 * @apiParam {int} language 语种，1中文，2英语
	 * @apiSuccess {int} statues 状态码
	 * @apiSuccess {array} data 问卷调查详情
	 * @apiSuccess {int} question_id 问卷id
	 * @apiSuccess {string} title 问卷标题
	 * @apiSuccess {string} description 问卷描述
	 * @apiSuccess {string} url 问卷链接
	 */
	public function question_list()
	{
		$this->validate([
			'language' => 'required|min:1|integer',
		]);
		$language = request('language');
		$p = request('p');
		$info = Queslist::where('status', 1)->where('language', $language)->select('title', 'description', 'id as question_id')->get()->toArray();
		foreach ($info as $k => $g) {
			$info[$k]['url'] = get_file_url("/api/question/quesinfo?p=" . $p . "&language=" . $language . "&id=" . $g['question_id']);
		}
		return response_json(1, $info);
	}

	/*
	 * 问卷提交处理
	 *
	 * */
	public function quesinfo()
	{
		if (request()->ajax()) {
			$num = request('num');
			$ques_id = request('ques_id');
			$arr['code'] = 'error';
			if (empty($num) || empty($ques_id)) {
				$arr['info'] = trans("base.qcscw");
				return $arr;
			}
			//已选择的选项id
			$add_id = [];
			//填写的文字信息
			$add_data = [];
			for ($i = 0; $i <= $num; $i++) {
				$error_i = $i + 1;
				$type = request('ques_type' . $i);
				if ($type == 1 || $type == 2) {
					if ($type == 1) {
						if (request('ques_option' . $i)) {
							$ls = explode('_', request('ques_option' . $i));
							if ($ls[0] == 'r') {
								$chose = $ls[1];
								$t_chose = 0;
							} else {
								$chose = 0;
								$t_chose = $ls[1];
							}
						} else {
							$arr['info'] = trans("base.qdi") . $error_i . trans("base.qswxz");
							return $arr;
						}
					} else {
						$chose = request('ques_option' . $i);
						$t_chose = request('t_ques_option' . $i);
					}
					if (empty($chose) && empty($t_chose)) {
						$arr['info'] = trans("base.qdi") . $error_i . trans("base.qswxz");
						return $arr;
					}
					if (!empty($t_chose)) {
						$text = request('ques_option_text' . $i);
						if (empty($text)) {

							$arr['info'] = trans("base.qqtxd") . $error_i . trans("base.qtdqtxx");

							return $arr;
						} else {
							$add_data[] = [
								'ques_id' => $ques_id,
								'quesinfo_id' => $i + 1,
								'text_info' => $text,
								'date_time' => date('Y-m-d H:i:s', time())
							];
							if (is_array($t_chose)) {
								array_push($add_id, $t_chose[0]);
							} else {
								array_push($add_id, $t_chose);
							}
						}
					}
				}
				if ($type == 1) {

					if (!empty($chose)) {
						array_push($add_id, $chose);
					}
				} elseif ($type == 2) {

					if (!empty($chose)) {
						foreach ($chose as $g) {
							array_push($add_id, $g);
						}
					}
				} elseif ($type == 3) {

					$text = request('ques_option_text' . $i);
					if (empty($text)) {
						$arr['info'] = trans("base.qqtxd") . $error_i . trans("base.qtdqtxx");
						return $arr;
					} else {
						$add_data[] = [
							'ques_id' => $ques_id,
							'quesinfo_id' => $i + 1,
							'text_info' => $text,
							'date_time' => date('Y-m-d H:i:s', time())
						];
					}
				}
			}
			QuesTextinfo::insert($add_data);
			QuesinfoOption::whereIn('id', $add_id)->increment('option_num', 1);
			Queslist::where('id', $ques_id)->increment('num', 1);
			$arr['code'] = 'success';
			return $arr;
		} else {
			//当前问卷ID
			$question_id = request('id');
			$end = request('end');
			if ($end == 1) {
				$arr['msg'] = trans("base.qmsgs");
				return view('api.question.ques_html_end', [
					'arr' => $arr,
					'p' => request('p'),
				]);
			}
			//获取当前语种的问卷
			$ques_info = Queslist::where('id', $question_id)->first();
			if (empty($ques_info) || $ques_info->status == 0) {
				$arr['msg'] = trans("base.qmsgn");
				return view('api.question.ques_html_end', [
					'arr' => $arr,
					'p' => request('p'),
				]);
			}
			$ques_id = $ques_info['id'];
			$arr['title'] = trans("base.qtitle");
			$arr['a'] = trans("base.qa");
			$arr['b'] = $ques_info['description'];
			$arr['c'] = trans("base.qc");
			$arr['d'] = trans("base.qd");
			$arr['e'] = trans("base.qe");
			$arr['f'] = trans("base.qf");
			$arr['g'] = trans("base.qg");
			$arr['h'] = trans("base.qh");
			$arr['i'] = trans("base.qi");
			$arr['j'] = trans("base.qj");
			//判断问卷下是否有题目
			$info = QuesinfoList::where('ques_id', $ques_id)->where('is_save', 1)->orderBy('id', 'asc')->get();
			foreach ($info as $k => $g) {
				$info[$k]['option_info'] = QuesinfoOption::where('quesinfo_id', $g['quesinfo_id'])->where('ques_id', $ques_id)->orderBy('option_type', 'asc')->orderBy('id', 'asc')->get()->toArray();
			}
			$num = count($info);

			if (empty($num)) {
				$arr['msg'] = trans("base.qzwtm");
				return view('api.question.ques_html_end', [
					'arr' => $arr,
					'p' => request('p'),
				]);
			}
			return view('api.question.ques_html_start', [
				'arr' => $arr,
				'info' => $info,
				'num' => $num,
				'ques_id' => $ques_id,
				'p' => request('p'),
				'language' => request('language')
			]);
		}
	}


	/**
	 * 02.问卷调查题库
	 *
	 * @apiDescription yyj 20180706
	 * @return \Illuminate\Http\JsonResponse
	 * @api {GET} /question/get_question 02.问卷调查题库new
	 * @apiGroup Question
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓
	 * @apiParam {int} language 语种，1中文，2英语
	 * @apiParam {int} question_id 问卷id
	 * @apiSuccess {array} data 题库详情(参照河北博物院)
	 */
	public function get_question()
	{
		$this->validate([
			'question_id' => 'required|min:1|integer',
		]);
		//当前问卷ID
		$question_id = request('question_id');
		//获取当前语种的问卷
		$ques_info = Queslist::where('id', $question_id)->first();
		if (empty($ques_info) || $ques_info->status == 0) {
			$arr['msg'] = trans("base.qmsgn");
			throw new ApiErrorException($arr['msg']);
		}
		$ques_id = $ques_info['id'];
		$arr['title'] = trans("base.qtitle");
		$arr['a'] = trans("base.qa");
		$arr['b'] = $ques_info['description'];
		$arr['c'] = trans("base.qc");
		$arr['d'] = trans("base.qd");
		$arr['e'] = trans("base.qe");
		$arr['f'] = trans("base.qf");
		$arr['g'] = trans("base.qg");
		$arr['h'] = trans("base.qh");
		$arr['i'] = trans("base.qi");
		$arr['j'] = trans("base.qj");
		//判断问卷下是否有题目
		$info = QuesinfoList::where('ques_id', $ques_id)->where('is_save', 1)->orderBy('id', 'asc')->get();
		foreach ($info as $k => $g) {
			$info[$k]['option_info'] = QuesinfoOption::where('quesinfo_id', $g['quesinfo_id'])->where('ques_id', $ques_id)->orderBy('option_type', 'asc')->orderBy('id', 'asc')->get()->toArray();
		}
		$num = count($info);

		if (empty($num)) {
			$arr['msg'] = trans("base.qzwtm");
			//throw new ApiErrorException($arr['msg']);
		}

		$data['arr'] = $arr;
		$data['info'] = $info;
		$data['num'] = $num;
		$data['ques_id'] = $ques_id;
		$data['p'] = request('p');
		$data['language'] = request('language');

		return response_json(1, $data);
	}


	/**
	 * 03.问卷调查答题提交new
	 *
	 * @apiDescription yyj 20180706
	 * @return \Illuminate\Http\JsonResponse
	 * @api {POST} /question/postquesinfo_new 03.问卷调查答题提交new
	 * @apiGroup Question
	 * @apiVersion 1.0.0
	 * @apiParam {string} p 平台，i：IOS，a：安卓
	 * @apiParam {string} data   数组(和林洋约定好的)(参照河北博物院)
	 * @apiParam {int} num  题数量
	 * @apiParam {int} ques_id  问卷调查ID
	 * @apiParam {int} language  语种
	 * @apiSuccess {int} statues 状态码
	 */
	public function postquesinfo_new()
	{
		$post_data = request('data');
		$num = request('num');
		$ques_id = request('ques_id');
		$arr['code'] = 'error';
		if (empty($num) || empty($ques_id)) {
			$arr['info'] = trans("base.qcscw");
			return response_json(1, $arr);
		}
		//已选择的选项id
		$add_id = [];
		//填写的文字信息
		$add_data = [];

		for ($i = 0; $i < $num; $i++) {
			$error_i = $i + 1;
			$type = $post_data[$i]['ques_type'];
			$chose = $t_chose = [];
			if ($type == 1 || $type == 2) {
				if ($type == 1) {
					if ($post_data[$i]['ques_option'][0]['option_id']) {
						if ($post_data[$i]['ques_option'][0]['option_type'] == 2) {
							$t_chose = $post_data[$i]['ques_option'][0]['option_id'];
							$text = $post_data[$i]['ques_option'][0]['ques_option_text'];
							if (!empty($text)) {
								$add_data[] = [
									'ques_id' => $ques_id,
									'quesinfo_id' => $i + 1,
									'text_info' => $text,
									'date_time' => date('Y-m-d H:i:s', time())
								];
							}

							array_push($add_id, $t_chose);

						} else {
							$chose = $post_data[$i]['ques_option'][0]['option_id'];
							array_push($add_id, $chose);
						}
					} else {
						$arr['info'] = trans("base.qdi") . $error_i . trans("base.qswxz");
						return response_json(1, $arr);
					}
				} elseif ($type == 2) {

					for ($q = 0; $q < count($post_data[$i]['ques_option']); $q++) {

						if ($post_data[$i]['ques_option'][$q]['option_id']) {
							if ($post_data[$i]['ques_option'][$q]['option_type'] == 2) {
								$chose[] = 0;
								$t_chose[] = $post_data[$i]['ques_option'][$q]['option_id'];
								$text = $post_data[$i]['ques_option'][$q]['ques_option_text'];
								if (!empty($text)) {
									$add_data[] = [
										'ques_id' => $ques_id,
										'quesinfo_id' => $i + 1,
										'text_info' => $text,
										'date_time' => date('Y-m-d H:i:s', time())
									];
								}
								array_push($add_id, $t_chose[$q]);
							} else {
								$chose[] = $post_data[$i]['ques_option'][$q]['option_id'];
								$t_chose[] = 0;
								array_push($add_id, $chose[$q]);
							}
						}
					}

				}
			} elseif ($type == 3) {

				$text = $post_data[$i]['ques_option'][0]['ques_option_text'];;
				if (!empty($text)) {
					$add_data[] = [
						'ques_id' => $ques_id,
						'quesinfo_id' => $i + 1,
						'text_info' => $text,
						'date_time' => date('Y-m-d H:i:s', time())
					];
				} else {

				}
			}

		}
		QuesTextinfo::insert($add_data);
		QuesinfoOption::whereIn('id', $add_id)->increment('option_num', 1);
		Queslist::where('id', $ques_id)->increment('num', 1);

		$arr['code'] = 'success';
		return response_json(1, $arr);

	}
}
