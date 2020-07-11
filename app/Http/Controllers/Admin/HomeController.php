<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Auth;

class HomeController extends BaseAdminController
{

	public function __construct()
	{
		parent::__construct();
	}

	/**
	 * 后台首页
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index()
	{
		return view('admin.home', [
			'user' => Auth::user()
		]);
	}

	public function welcome()
	{
	}


	public function execl_test(){
		$file=base_path('public/import/1.xlsx');
		// 判断文件是否存在
		if (!file_exists($file)) {
			exit("not found".$file);
		}
		//获取文件后缀
		$hz=strstr($file,'.');
		if($hz=='.xls'){
			$reader = \PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
		}
		elseif($hz=='.xlsx'){
			$reader = \PHPExcel_IOFactory::createReader('Excel2007'); //设置以Excel2007格式(2007以上工作簿)
		}
		else{
			exit("文件格式错误");
		}
		$PHPExcel = $reader->load($file); // 载入excel文件
		$sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
		$highestRow = $sheet->getHighestRow(); // 取得总行数
		$highestColumm = $sheet->getHighestColumn(); // 取得总列数
		$highestColumm= \PHPExcel_Cell::columnIndexFromString($highestColumm); //字母列转换为数字列 如:AA变为27

		/** 循环读取每个单元格的数据 并将其转化为数组*/
		for ($row = 1; $row <= $highestRow; $row++){//行数是以第1行开始
			for ($column = 0; $column < $highestColumm; $column++) {//列数是以第0列开始
				$columnName = \PHPExcel_Cell::stringFromColumnIndex($column);
				$info=$sheet->getCellByColumnAndRow($column, $row)->getValue();
				if($info instanceof PHPExcel_RichText){
					//富文本转换字符串
					$info = $info->__toString();
				}
				$info_array[$row-1][$columnName]=$info;
			}
		}
		$objPHPExcel = new \PHPExcel();
		$objPHPExcel->setActiveSheetIndex(0);
		$objPHPExcel->getActiveSheet()->setTitle('统计报表');
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(40);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);


		$fileext = '.xlsx';
		$filename = time() . $fileext;
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
			$title='统计报表.xlsx';
			return response()->download($pathToFile,$title, $headers);
		} else {
			return response_json(0, [], '导出错误，请刷新页面后重试');
		}
	}
}
