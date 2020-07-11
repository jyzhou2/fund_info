<?php

/**
 * 和业务相关的辅助方法
 */

if (!function_exists('response_json')) {

	/**
	 * 公共数据返回方法
	 *
	 * @author lxp 20170112
	 * @param int $statusCode 状态码，成功为1，失败为0
	 * @param array $data 数据数组
	 * @param string $message 文字信息
	 * @return \Illuminate\Http\JsonResponse
	 */
	function response_json($statusCode = 1, $data = [], $message = '')
	{
		$headers = [];
		$agent = request()->server('HTTP_USER_AGENT');
		if ($agent && preg_match_all("/MSIE|IEMobile|MSIEMobile/i", $agent) > 0) {
			// 解决IE下json数据变为下载文件的问题
			$headers['Content-Type'] = 'text/html; charset=utf-8';
		}

		// 跨域配置，Web和触摸屏允许跨域
		//		if (env('ALLOW_ORIGIN') && in_array(request('p'), [
		//				't',
		//				'w'
		//			])
		if (env('ALLOW_ORIGIN')) {
			$headers['Access-Control-Allow-Origin'] = env('ALLOW_ORIGIN');
		}

		// 处理数据
		$data = r_format_data($data);

		$return_data = [
			'status' => $statusCode,
			'msg' => $message
		];
		if ($statusCode == 1) {
			$return_data['data'] = $data;
		}

		// 处理debug数据
		if (app()->bound('debugbar') && app('debugbar')->isEnabled()) {
			$return_data['_debug'] = r_format_debugbardata(app('debugbar')->getData());
		}

		return response()->json($return_data)->withHeaders($headers);
	}

	/**
	 * 格式化数据
	 *
	 * @author lxp 20170822
	 * @param mixed $data
	 * @return array|int|string
	 */
	function r_format_data($data)
	{
		if (is_null($data)) {
			$data = '';
		} elseif (is_numeric($data)) {
			//			$data = $data + 0;
		} elseif (is_array($data)) {
			foreach ($data as $k => $v) {
				$data[$k] = r_format_data($v);
			}
		} elseif ($data instanceof \Illuminate\Database\Eloquent\Collection || $data instanceof \Illuminate\Database\Eloquent\Model) {
			$data = r_format_data($data->toArray());
		}

		return $data;
	}

	/**
	 * 返回需要的debug数据
	 *
	 * @author lxp 20180409
	 * @param array $debugbardata
	 * @param bool $all
	 * @return array
	 */
	function r_format_debugbardata($debugbardata, $all = false)
	{
		// 是否返回全部debug数据
		if ($all == true) {
			return $debugbardata;
		}

		$time = [];
		$time['duration_str'] = $debugbardata['time']['duration_str'];
		if (is_array($debugbardata['time']['measures'])) {
			$time['measures'] = [];
			foreach ($debugbardata['time']['measures'] as $tk => $tv) {
				$time['measures'][$tk] = [
					'label' => $tv['label'],
					'duration_str' => $tv['duration_str']
				];
			}
		}
		$memory = $debugbardata['memory']['peak_usage_str'];
		$queries = [
			'nb_statements' => $debugbardata['queries']['nb_statements'],
			'nb_failed_statements' => $debugbardata['queries']['nb_failed_statements'],
			'accumulated_duration_str' => $debugbardata['queries']['accumulated_duration_str']
		];
		if (is_array($debugbardata['queries']['statements'])) {
			foreach ($debugbardata['queries']['statements'] as $qk => $qv) {
				$queries['statements'][$qk] = [
					'sql' => $qv['sql'],
					'duration_str' => $qv['duration_str'],
					'stmt_id' => $qv['stmt_id']
				];
			}
		};

		return [
			'time' => $time,
			'memory' => $memory,
			'queries' => $queries
		];
	}
}

if (!function_exists('get_api_token')) {

	/**
	 * 生成API TOKEN
	 *
	 * @author lxp 20170805
	 * @param int $uid 用户id
	 * @return string
	 */
	function get_api_token($uid)
	{
		return md5(\Illuminate\Support\Str::random(60) . date('U') . '__' . $uid);
	}
}

if(!function_exists('get_ueditor_content')){
	/**
	 * 编辑器替换图片路径
	 *
	 * @author yyj 20190319
	 * @param string $content 编辑器内容
	 * @return string
	 */
	function get_ueditor_content($content){
		preg_match_all('/src=[\'|"][^"\']+\?f([0-9]+)[^"\']*[\'|"]/i', $content, $arr);
		if(isset($arr[0])&&is_array($arr[0])){
			foreach ($arr[0] as $k=>$g){
				if(strpos($g,'http') === false){
					$str = str_replace('src="', '',$g);
					$str = strstr($str, '"',true);
					$str=str_replace('"','',$str);
					$content=str_replace($str,get_file_url($str),$content);
				}
			}
		}
		return $content;
	}
}