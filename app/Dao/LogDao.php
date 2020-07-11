<?php

namespace App\Dao;

use App\Exceptions\ApiErrorException;

/**
 * 日志记录调用模型
 *
 * @author yyj 20171201
 */
class LogDao
{

	/**
	 * 日志记录
	 *
	 * @author yyj 20171201
	 * @param string $file_name 要生成的日志名称
	 * @param array|string $log_info 要记录的日志内容
	 */
	public static function save_log($file_name,$log_info){
		$logObj = app('logext');
		$logObj->init($file_name);
		if(!is_array($log_info)){
			$logObj->logbuffer('result',$log_info);
		}
		else{
			foreach ($log_info as $k=>$g){
				$logObj->logbuffer($k,$g);
			}
		}
		$logObj->logend();
	}
}
