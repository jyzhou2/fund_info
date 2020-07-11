<?php

namespace App\Http\Controllers\Admin\Question;

use App\Http\Controllers\Admin\BaseAdminController;
use App\Models\Queslist;
use App\Models\QuesinfoList;
use App\Models\QuesinfoOption;
use App\Models\QuesTextinfo;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * 问卷调查控制器
 *
 * @author yyj
 * @package App\Http\Controllers\Data
 */
class QuestionController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 问卷管理
	 *
	 * @author ljy 20170816
	 */
	public function ques_list(Request $request)
	{
		if (request()->ajax()) {
			$id = $request->input('del');
			$r = Queslist::where('id', $id)->delete();
			if ($r) {
				//删除问卷相关的题目及选项
				QuesinfoOption::where('ques_id', $id)->delete();
				QuesinfoList::where('ques_id', $id)->delete();
				QuesTextinfo::where('ques_id', $id)->delete();

				return $this->success();
			} else {
				return $this->error();
			}
		} else {

			$query = Queslist::orderBy('date_time');
			$data = $query->paginate(parent::$perpage);
			foreach ($data as $k => $g) {
				$where3['ques_id'] = $g->id;
				$where3['is_save'] = 1;
				$data[$k]['ques_num'] = QuesinfoList::where('ques_id', $g->id)->where('is_save', 1)->count();
			}
			return view('admin.question.ques_list', [
				'info' => $data
			]);
		}
	}

	/**
	 * ajax问卷状态更改 只允许一个是进行状态
	 *
	 * @author ljy 20170815
	 */
	public function ques_status(Request $request)
	{
		if (request()->ajax()) {
			$id = $request->input('id');
			$status = $request->input('status');
			//获取题目数量
			$num = QuesinfoList::where('ques_id', $id)->where('is_save', 1)->count();
			if ($num == 0) {
				return $this->error('请先添加要调查的问题！');
			}
			$data['status'] = $status;
			$r = Queslist::where('id', $id)->update($data);
			if ($r) {
				/*
				 * 可以多个问卷同时调查
				if ($status == 1) {
					$data2['status'] = 0;
					$r = Queslist::where('id','<>',$id)->update($data2);
				}
				*
				 *
				 */
				return $this->success();
			} else {
				return $this->error();
			}
		}
	}

	/**
	 * ajax获取问卷详情
	 *
	 * @author ljy 20170816
	 */
	public function ajax_ques(Request $request)
	{
		if (request()->ajax()) {

			$id = $request->input('id');
			$info = Queslist::where('id', $id)->first();

			if (empty($info)) {
				$data['status'] = 'false';
				$data['msg'] = '参数错误';
				return $data;
			}
			if ($info['status'] == 0) {
				$info['status'] = '未开始';
			} else {
				$info['status'] = '进行中';
			}
			$info['date_time'] = $info['date_time'];
			//			$info['start_time'] = date('Y-m-d H:i:s', $info['start_time']);
			//			$info['end_time'] = date('Y-m-d H:i:s', $info['end_time']);

			$info['ques_num'] = QuesinfoList::where('ques_id', $info['id'])->count();
			return $info;
		}
	}

	/**
	 * 编辑问卷
	 *
	 * @author ljy 20170815
	 */
	public function edit_ques(Request $request)
	{
		if (request()->ajax()) {
			$title = $request->input('title');
			$id = $request->input('id');
			$description = $request->input('description');

			if (empty($id)) {
				$error['status'] = 'false';
				$error['msg'] = '参数错误';
				return $error;
			}

			$data['title'] = $title;
			$data['language'] = request("language");
			$data['description'] = $description;
			$data['date_time'] = date("Y-m-d H:i:s", time());
			$data['user_login'] = Auth::user()->username;

			if ($id == 'add') {

				$data['num'] = 0;
				$result = Queslist::insertGetId($data);

				//计划创建完毕自动创建一道题目
				//新增题目
				$data2['ques_id'] = $result;
				$data2['quesinfo_id'] = 1;
				$data2['date_time'] = date("Y-m-d H:i:s", time());
				$data2['question'] = '';
				$data2['is_save'] = 0;
				$data2['type'] = 1;

				$quesinfo_id = QuesinfoList::insertGetId($data2);

				//新增两个基本选项
				$data3['quesinfo_id'] = 1;
				$data3['option_info'] = '';
				$data3['option_num'] = 0;
				$data3['option_type'] = 1;
				$data3['ques_id'] = $result;
				QuesinfoOption::insert($data3);
				QuesinfoOption::insert($data3);

			} else {

				$result = Queslist::where('id', $id)->update($data);

			}
			if ($result) {
				$success['status'] = 'true';
				$success['msg'] = '操作成功';
				return $success;
			} else {
				return $this->error();
			}
		} else {

			$id = $request->input('id');
			if ($id != 'add') {
				$info = Queslist::where('id', $id)->first();
				//判断问卷是否开始
				if ($info['start_time'] <= date("Y-m-d H:i:s", time())) {
					//问卷已经开始只能修改结束时间
					$is_start = 1;
				} else {
					$is_start = 0;
				}
			} else {
				$is_start = '';
				$info = '';
			}
			return view('admin.question.edit_ques', [
				'is_start' => $is_start,
				'info' => $info,
				'id' => $id
			]);

		}
	}

	/**
	 * 问卷统计
	 *
	 * @author ljy 20170816
	 */
	public function ques_info(Request $request)
	{
		$ques_id = $request->input('id');
		$info = QuesinfoList::where('ques_id', $ques_id)->where('is_save', 1)->orderBy('id', 'asc')->get()->toArray();
		foreach ($info as $k => $g) {
			if ($g['type'] == 3) {
				$info[$k]['text_info'] = QuesTextinfo::where('quesinfo_id', $g['quesinfo_id'])->where('ques_id', $ques_id)->orderBy('id', 'desc')->limit(10)->get()->toArray();
				$info[$k]['text_num'] = $info[$k]['text_info']->count();
			} else {
				$info[$k]['option_info'] = QuesinfoOption::where('quesinfo_id', $g['quesinfo_id'])->where('ques_id', $ques_id)->orderBy('id', 'asc')->get()->toArray();
				$info[$k]['text_num'] = 0;
			}
		}
		return view('admin.question.ques_info', [
			'info' => $info,
			'id' => $ques_id,
		]);
	}

	/*
	 * 问卷作答详情统计
	 *
	 * @author ljy 20170816
	 *
	 * */
	public function ques_textinfo(Request $request)
	{

		$quesinfo_id = $request->input('quesinfo_id');
		$ques_id = $request->input('ques_id');
		$where['quesinfo_id'] = $quesinfo_id;
		$where['ques_id'] = $ques_id;

		$query = QuesTextinfo::where('quesinfo_id', $quesinfo_id)->where('ques_id', $ques_id)->orderBy('id', 'desc');
		$info = $query->paginate(parent::$perpage);

		return view('admin.question.ques_textinfo', [
			'info' => $info,
			'id' => $ques_id,
		]);
	}

	/**
	 * ajax显示所有题目
	 *
	 * @author ljy 20170816
	 */
	public function ajax_quesinfo(Request $request)
	{
		if (request()->ajax()) {
			$ques_id = $request->input('ques_id');
			$info = QuesinfoList::where('ques_id', $ques_id)->orderBy('id', 'asc')->get();

			foreach ($info as $k => $g) {
				$info[$k]['option_info'] = QuesinfoOption::where('quesinfo_id', $g['quesinfo_id'])->where('ques_id', $ques_id)->orderby('id', 'asc')->get();
				if (is_null($info[$k]['question'])) {
					$info[$k]['question'] = "";
				}
				foreach ($info[$k]['option_info'] as $kk => $vv) {
					if (is_null($vv['option_info'])) {
						$info[$k]['option_info'][$kk]['option_info'] = "";
					}
				}

				$info[$k]['option_count'] = count($info[$k]['option_info']);
			}
			$queslist['count'] = count($info);
			$queslist['infolist'] = $info;
			return $queslist;
			//			$this->ajaxReturn($queslist, 'JSON');
		}
	}

	/**
	 * ajax获取当前表单信息
	 *
	 * @author 20170815
	 */
	public function ajax_forminfo(Request $request)
	{
		if (request()->ajax()) {
			$do_type = $request->input('type');
			$ques_count = $request->input('ques_count');
			$ques_id = $request->input('ques_id');
			for ($i = 1; $i <= $ques_count; $i++) {
				if ($do_type == 'del') {
					$del_id = $request->input('del_id');
				} else {
					$del_id = 0;
				}
				if ($i != $del_id) {
					$type = $request->input('type' . $i);
					switch ($type) {
						case 1:
							$post_name = 'r_question' . $i . 'option';
							$question_name = 'r_question' . $i;
							break;
						case 2:
							$post_name = 'c_question' . $i . 'option';
							$question_name = 'c_question' . $i;
							break;
						case 3:
							$question_name = 't_question' . $i;
							break;
					}
					$question = $request->input($question_name);
					if ($type != 3) {
						$option = $request->input($post_name);
						foreach ($option as $g) {
							if ($g == "" || is_null($g) || $g == "null") {
								$g = "";
							}

							$option_type = ($g == '其他____________' || $g == '不满意(请注明原因)____________') ? 2 : 1;
							$option_arr[] = [
								'quesinfo_id' => $i,
								'option_info' => $g,
								'option_num' => 0,
								'option_type' => $option_type,
								'ques_id' => $ques_id,
							];
						}
					} else {
						$option_arr = [];
					}
					if ($question == "" || is_null($question) || $question == "null") {
						$question = "";
					}
					$info[] = [
						'ques_id' => $ques_id,
						'quesinfo_id' => $i,
						'date_time' => date("Y-m-d H:i:s", time()),
						'question' => $question,
						'is_save' => 1,
						'type' => $type,
						'option_info' => $option_arr,
						'option_count' => count($option_arr),
					];
					unset($option_arr);

				}

			}

			if ($do_type == 'add') {
				$info[] = [
					'ques_id' => $ques_id,
					'quesinfo_id' => $ques_count + 1,
					'date_time' => date("Y-m-d H:i:s", time()),
					'question' => "",
					'is_save' => 0,
					'type' => 1,
					'option_info' => [
						[
							'quesinfo_id' => $ques_count + 1,
							'option_info' => "",
							'option_num' => 0,
							'option_type' => 1,
							'ques_id' => $ques_id,
						],
						[
							'quesinfo_id' => $ques_count + 1,
							'option_info' => "",
							'option_num' => 0,
							'option_type' => 1,
							'ques_id' => $ques_id,
						]
					],
					'option_count' => 2,
				];
			}
			$queslist['count'] = count($info);
			$queslist['infolist'] = $info;
			$queslist['type'] = $do_type;
			$queslist['del_id'] = $del_id;
			return $queslist;
		}
	}

	/**
	 * 问卷题目管理
	 *
	 * @author 20170816
	 */
	public function quesinfo_list(Request $request)
	{
		if (request()->ajax()) {
			$quesinfo_id = I('post.quesinfo_id');
			$ques_id = I(post . ques_id);
			$where['quesinfo_id'] = $quesinfo_id;
			$where['ques_id'] = $ques_id;
			$r = M('project_quesinfo_list')->where($where)->delete();
			if ($r) {
				M('project_quesinfo_option')->where($where)->delete();
				$info['code'] = 'success';
			} else {
				$info['code'] = 'error';
			}
			$this->ajaxReturn($info, 'JSON');
		} else {
			$ques_id = $request->input('id');
			$title = $request->input('title');
			if (empty($ques_id) || empty($title)) {
				return $this->error('参数错误');
			}
			return view('admin.question.quesinfo_list', [
				'title' => $title,
				'ques_id' => $ques_id,
			]);
		}
	}

	/**
	 * 编辑问题
	 *
	 * @author ljy 20170816
	 */
	public function edit_quesinfo(Request $request)
	{
		if (request()->ajax()) {
			$ques_count = $request->input('ques_count');
			$ques_id = $request->input('ques_id');
			if (empty($ques_count)) {
				$error['status'] = 'false';
				$error['info'] = '请添加题目';
				return $error;
			}
			//题目数组
			$question_arr = [];
			//选项数组
			$option_arr = [];
			for ($i = 1; $i <= $ques_count; $i++) {
				$type = $request->input('type' . $i);
				if (empty($type)) {
					$error['status'] = 'false';
					$error['info'] = '参数错误';
					return $error;
				}
				switch ($type) {
					case 1:
						$post_name = 'r_question' . $i . 'option';
						$question_name = 'r_question' . $i;
						break;
					case 2:
						$post_name = 'c_question' . $i . 'option';
						$question_name = 'c_question' . $i;
						break;
					case 3:
						$question_name = 't_question' . $i;
						break;
					default:
						$error['status'] = 'false';
						$error['info'] = '参数错误';
						return $error;
						break;
				}
				$question = $request->input($question_name);
				if (empty($question) || is_null($question)) {
					$error['status'] = 'false';
					$error['info'] = '第' . $i . '题的题目不能为空';
					return $error;
				}
				$ques_key = array_search($question, $question_arr);
				if ($ques_key !== false) {
					$error['status'] = 'false';
					$error['info'] = '第' . $i . '题与第' . ($ques_key + 1) . '题相同，请修改';
					return $error;
				}
				if ($type != 3) {
					$option = $request->input($post_name);
					foreach ($option as $g) {
						if ($g == '' || is_null($g) || $g == "null") {
							$error['status'] = 'false';
							$error['info'] = '第' . $i . '题存在空值的选项';
							return $error;
						}
						$option_type = ($g == '其他____________' || $g == '不满意(请注明原因)____________') ? 2 : 1;
						$option_arr[] = [
							'quesinfo_id' => $i,
							'option_info' => $g,
							'option_num' => 0,
							'option_type' => $option_type,
							'ques_id' => $ques_id,
						];
					}
					if (count($option) != count(array_unique($option))) {
						$error['status'] = 'false';
						$error['info'] = '第' . $i . '题存在相同的选项';
						return $error;
					}
				}
				$question_arr[] = [
					'ques_id' => $ques_id,
					'quesinfo_id' => $i,
					'date_time' => date("Y-m-d H:i:s", time()),
					'question' => $question,
					'is_save' => 1,
					'type' => $type,
				];
			}
			QuesinfoList::where('ques_id', $ques_id)->delete();

			QuesinfoOption::where('ques_id', $ques_id)->delete();
			QuesTextinfo::where('ques_id', $ques_id)->delete();

			QuesinfoList::insert($question_arr);
			QuesinfoOption::insert($option_arr);

			$data['num'] = 0;
			Queslist::where('id', $ques_id)->update($data);

			$success['status'] = 'true';
			$success['info'] = '保存成功';
			return $success;
		}
	}

	/**
	 * 问卷统计结果导出
	 *
	 * @author ljy 20170816
	 * @return json
	 */
	public function ques_export(Request $request)
	{

		$id = $request->input('id');
		//获取问卷标题
		$where['id'] = $id;
		$ques_info = Queslist::where('id', $id)->select('title', 'num')->first();
		$title = $ques_info['title'] . '(' . $ques_info['num'] . '人参与)';
		//获取问卷详情
		$info = QuesinfoList::where('ques_id', $id)->where('is_save', 1)->orderBy('id', 'asc')->get();

		foreach ($info as $k => $g) {
			if ($g['type'] == 3) {

				$info[$k]['text_info'] = QuesTextinfo::where('quesinfo_id', $g['quesinfo_id'])->where('ques_id', $id)->orderBy('id', 'desc')->get();
			} else {

				$info[$k]['option_info'] = QuesinfoOption::where('quesinfo_id', $g['quesinfo_id'])->where('ques_id', $id)->orderBy('id', 'asc')->get();

			}
		}
		if (empty($info)) {
			$this->error('该问卷下没有题目');
		} else {
			$objPHPExcel = new \PHPExcel();
			$objPHPExcel->setActiveSheetIndex(0);
			$objPHPExcel->getActiveSheet()->setTitle('问卷票数详情');
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(100);
			//设置标题
			$objPHPExcel->getActiveSheet()->setCellValue('A1', $title);

			$objPHPExcel->getActiveSheet()->mergeCells('A1:B1');//合并单元格
			$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);//设置高度
			//设置样式
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('宋体');
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
			$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
			$i = 2;
			//将数据库数据写入
			$ques_arr = [
				'1' => '(单选)',
				'2' => '(可多选)',
				'3' => '(问答)'
			];
			$option_en = [
				'1' => 'A',
				'2' => 'B',
				'3' => 'C',
				'4' => 'D',
				'5' => 'E',
				'6' => 'F',
				'7' => 'G',
				'8' => 'H',
				'9' => 'I',
				'10' => 'J',
				'11' => 'K',
				'12' => 'L',
				'13' => 'M',
				'14' => 'N'
			];
			foreach ($info as $k => $g) {
				//题目标题
				$quesinfo_title = "第" . $g['quesinfo_id'] . "题." . $g['question'] . $ques_arr[$g['type']];
				$objPHPExcel->getActiveSheet()->setCellValue("A{$i}", $quesinfo_title);
				$objPHPExcel->getActiveSheet()->mergeCells("A{$i}:B{$i}");//合并单元格
				$objPHPExcel->getActiveSheet()->getStyle("A{$i}")->getFont()->setName('宋体');
				$objPHPExcel->getActiveSheet()->getStyle("A{$i}")->getFont()->setSize(16);
				$objPHPExcel->getActiveSheet()->getStyle("A{$i}")->getFont()->setBold(true);
				$objPHPExcel->getActiveSheet()->getStyle("A{$i}")->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
				$objPHPExcel->getActiveSheet()->getStyle("A{$i}")->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
				$i = $i + 1;
				//设置题目选项及票数
				if ($g['type'] == 3) {
					$objPHPExcel->getActiveSheet()->setCellValue("A{$i}", "参与人数");
					$objPHPExcel->getActiveSheet()->setCellValue("B{$i}", count($g['text_info']) . '人');
					$i = $i + 1;
				} else {
					foreach ($g['option_info'] as $kk => $gg) {
						$option_id = $kk + 1;
						$option_info = "选项{$option_en[$option_id]}:{$gg['option_info']}";
						$objPHPExcel->getActiveSheet()->setCellValue("A{$i}", $option_info);
						$objPHPExcel->getActiveSheet()->setCellValue("B{$i}", "{$gg['option_num']}票");
						$i = $i + 1;
					}
				}
			}
			//所有其他选项及问答题详情
			$where3['ques_id'] = $id;
			$text_info = QuesTextinfo::where('ques_id', $id)->get();
			if (!empty($text_info[0])) {
				foreach ($text_info as $k => $v) {
					$new[$v['quesinfo_id']]['quesinfo_id'] = $v['quesinfo_id'];
				}
				$a = 1;
				foreach ($new as $g) {
					$objPHPExcel->createSheet();
					$objPHPExcel->setActiveSheetIndex($a);
					$objPHPExcel->getActiveSheet()->setTitle("第" . $g['quesinfo_id'] . "题作答详情");
					//获取题目标题
					$question = QuesinfoList::where('ques_id', $id)->where('quesinfo_id', $g['quesinfo_id'])->value('question');
					//获取作答详情
					$ques_textinfo = QuesTextinfo::where('ques_id', $id)->where('quesinfo_id', $g['quesinfo_id'])->orderBy('id', 'desc')->get();

					$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(100);
					$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(100);
					//设置标题
					$objPHPExcel->getActiveSheet()->setCellValue('A1', $question . '作答详情');
					$objPHPExcel->getActiveSheet()->mergeCells('A1:B1');//合并单元格
					$objPHPExcel->getActiveSheet()->getRowDimension(1)->setRowHeight(30);//设置高度
					//设置样式
					$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setName('宋体');
					$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(20);
					$objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
					$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setVertical(\PHPExcel_Style_Alignment::VERTICAL_CENTER);//垂直居中
					$objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setHorizontal(\PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
					$i = 2;
					foreach ($ques_textinfo as $gg) {
						$objPHPExcel->getActiveSheet()->setCellValue("A" . $i, $gg['text_info']);
						$objPHPExcel->getActiveSheet()->setCellValue("B" . $i, $gg['date_time']);
						$i = $i + 1;
					}
					$a = $a + 1;
				}
			}
			$fileext = '.xlsx';
			$filename = $title . $fileext;
			$objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
			// 文件保存路径
			$path = preg_replace('(/+)', '/', storage_path('/tempdoc'));
			if (!is_dir($path)) {
				@mkdir($path, 0755, true);
			}
			$pathToFile = $path . '/' . substr(md5($filename), 0, 10) . date('YmdHis') . rand(1000, 9999) . $fileext;
			$objWriter->save($pathToFile);

			if (file_exists($pathToFile)) {
				$headers = [
					'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
					'Content-Length' => filesize($pathToFile),
					'Content-Disposition' => 'attachment; filename="' . $filename . '"'
				];
				return response()->download($pathToFile, $filename, $headers);
			} else {
				return response_json(0, [], '导出错误，请刷新页面后重试');
			}
		}

	}

}