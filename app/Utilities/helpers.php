<?php

if (!function_exists('is_mobile')) {

	/**
	 * 验证是否为手机号
	 *
	 * @author lxp 20160204
	 * @param string $mobile
	 * @return bool
	 */
	function is_mobile($mobile)
	{
		if (!is_numeric($mobile)) {
			return false;
		}
		if (preg_match('/^1\d{10}$/', $mobile)) {
			return true;
		} else {
			return false;
		}
	}
}

if (!function_exists('is_email')) {

	/**
	 * 验证邮件地址
	 *
	 * @author lxp 20160328
	 * @param string $email 需要验证的邮件地址
	 * @return bool
	 */
	function is_email($email)
	{
		$chars = "/^([a-z0-9+_]|\\-|\\.)+@(([a-z0-9_]|\\-)+\\.)+[a-z]{2,5}\$/i";
		if (strpos($email, '@') !== false && strpos($email, '.') !== false) {
			if (preg_match($chars, $email)) {
				return true;
			}
		}
		return false;
	}
}

if (!function_exists('get_password')) {

	/**
	 * 取得加密后的密码
	 *
	 * @author lxp 20160603
	 * @param string $password 原密码
	 * @param string $salt 加密盐
	 * @return string
	 */
	function get_password($password, $salt)
	{
		return md5(md5($password) . $salt);
	}
}

if (!function_exists('file_size_format')) {

	/**
	 * 文件大小格式化
	 *
	 * @author lxp 20160627
	 * @param int $size 文件大小
	 * @param int $dec 小数点后保留位数
	 * @return string
	 */
	function file_size_format($size = 0, $dec = 2)
	{
		$unit = [
			"Byte",
			"KB",
			"MB",
			"GB",
			"TB",
			"PB"
		];
		$pos = 0;
		while ($size >= 1024) {
			$size /= 1024;
			$pos++;
		}
		$result['size'] = round($size, $dec);
		$result['unit'] = $unit[$pos];
		return $result['size'] . ' ' . $result['unit'];
	}
}

if (!function_exists('xml_to_array')) {

	/**
	 * xml转数组
	 *
	 * @author lxp 20170304
	 * @param string $xml
	 * @return array
	 */
	function xml_to_array($xml)
	{
		if (!$xml) {
			return [];
		}
		//禁止引用外部xml实体
		libxml_disable_entity_loader(true);
		$array = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
		return $array;
	}
}

if (!function_exists('card_num_hidden')) {
	/**
	 * 证件号码，手机号码隐藏
	 *
	 * @author lxp 20170901
	 * @param string $num
	 * @param int $f_len
	 * @param int $l_len
	 * @return string
	 */
	function card_num_hidden($num, $f_len = 3, $l_len = 4)
	{
		$num_hidden = '';
		for ($i = 0; $i < strlen($num); $i++) {
			if ($i < $f_len || $i > strlen($num) - 1 - $l_len) {
				$num_hidden .= $num[$i];
			} else {
				$num_hidden .= '*';
			}
		}
		return $num_hidden;
	}
}

if (!function_exists('is_idcard')) {
	/**
	 * 身份证号验证
	 *
	 * @author lxp 20161105
	 * @param string $idcard
	 * @param bool $return_bool 是否返回bool值
	 * @return bool|array
	 */
	function is_idcard($idcard, $return_bool = false)
	{
		$idcard = strtoupper($idcard);

		$city = [
			11 => "北京",
			12 => "天津",
			13 => "河北",
			14 => "山西",
			15 => "内蒙古",
			21 => "辽宁",
			22 => "吉林",
			23 => "黑龙江",
			31 => "上海",
			32 => "江苏",
			33 => "浙江",
			34 => "安徽",
			35 => "福建",
			36 => "江西",
			37 => "山东",
			41 => "河南",
			42 => "湖北",
			43 => "湖南",
			44 => "广东",
			45 => "广西",
			46 => "海南",
			50 => "重庆",
			51 => "四川",
			52 => "贵州",
			53 => "云南",
			54 => "西藏",
			61 => "陕西",
			62 => "甘肃",
			63 => "青海",
			64 => "宁夏",
			65 => "新疆",
			71 => "台湾",
			81 => "香港",
			82 => "澳门",
			91 => "国外"
		];

		if (!$idcard || !preg_match('/^\d{6}(18|19|20)?\d{2}(0[1-9]|1[012])(0[1-9]|[12]\d|3[01])\d{3}(\d|X)$/i', $idcard)) {
			return false;
		} else if (!isset($city[substr($idcard, 0, 2)])) {
			return false;
		} else {
			//18位身份证需要验证最后一位校验位
			if (mb_strlen($idcard) == 18) {
				$codeArray = str_split($idcard);
				//加权因子
				$factor = [
					7,
					9,
					10,
					5,
					8,
					4,
					2,
					1,
					6,
					3,
					7,
					9,
					10,
					5,
					8,
					4,
					2
				];
				//校验位
				$parity = [
					1,
					0,
					'X',
					9,
					8,
					7,
					6,
					5,
					4,
					3,
					2
				];
				$sum = 0;
				$ai = 0;
				$wi = 0;
				for ($i = 0; $i < 17; $i++) {
					$ai = $codeArray[$i];
					$wi = $factor[$i];
					$sum += $ai * $wi;
				}
				$last = $parity[$sum % 11];
				if ($parity[$sum % 11] != $codeArray[17]) {
					return false;
				}
			} elseif (mb_strlen($idcard) == 15) {
				// 15位身份证
				return false;
			} else {
				return false;
			}
		}

		if ($return_bool) {
			return true;
		} else {
			return [
				'city' => $city[substr($idcard, 0, 2)],
				'age' => date('Y') - substr($idcard, 6, 4),
				'sex' => (substr($idcard, 16, 1) % 2 == 0) ? 2 : 1,
				'birthday' => substr($idcard, 10, 2) . '-' . substr($idcard, 12, 2)
			];
		}
	}
}

if (!function_exists('is_passport')) {
	/**
	 * 护照验证
	 *
	 * @author lxp 20190221
	 * @param string $passport
	 * @return false|int
	 */
	function is_passport($passport)
	{
		return preg_match('/^[a-z0-9]{5,17}$/i', $passport);
	}
}

if (!function_exists('m_file_exists')) {
	/**
	 * 判断文件是否存在，包含远程文件
	 *
	 * @author lxp 20170915
	 * @param string $file
	 * @return bool
	 */
	function m_file_exists($file)
	{
		if (preg_match('/^http(s)?:\/\//', $file)) {
			//远程文件
			if (ini_get('allow_url_fopen')) {
				if (@fopen($file, 'r')) {
					return true;
				}
			} else {
				$parseurl = parse_url($file);
				$host = $parseurl['host'];
				$path = $parseurl['path'];
				$fp = fsockopen($host, 80, $errno, $errstr, 5);
				if (!$fp) {
					return false;
				}
				fputs($fp, "GET {$path} HTTP/1.1 \r\nhost:{$host}\r\n\r\n");
				if (preg_match('/HTTP\/1.1 200/', fgets($fp, 1024))) {
					return true;
				}
			}
			return false;
		}
		return file_exists($file);
	}
}

if (!function_exists('m_mkdir')) {
	/**
	 * 创建目录，并设置权限
	 *
	 * @author lxp 20171103
	 * @param $dir
	 * @param int $mode
	 */
	function m_mkdir($dir, $mode = 0777)
	{
		if ($dir && !is_dir($dir)) {
			@mkdir($dir, $mode, true);
			@chmod($dir, $mode);
		}
	}
}

if (!function_exists('m_phpcode_format')) {
	/**
	 * 格式化php代码，去掉注释，多用于项目申报
	 *
	 * @author lxp 20180112
	 * @param string $content
	 * @return mixed|string
	 */
	function m_phpcode_format($content)
	{
		$stripStr = '';
		// 分析php源码
		$tokens = token_get_all($content);

		//	dd($tokens);
		$last_space = false;
		for ($i = 0, $j = count($tokens); $i < $j; $i++) {
			if (is_string($tokens[$i])) {
				$last_space = false;
				if (trim($tokens[$i])) {
					$stripStr .= $tokens[$i];
				}
			} else {
				switch ($tokens[$i][0]) {
					// 过滤各种PHP注释
					case T_COMMENT:
					case T_DOC_COMMENT:
						break;
					// 处理空格
					case T_WHITESPACE:
						if (!$last_space) {
							$str = $tokens[$i][1];
							$str = str_replace('    ', '	', $str);
							$stripStr .= $str;
							$last_space = true;
						}
						break;
					case T_START_HEREDOC:
						$stripStr .= "<<<THINK\n";
						break;
					case T_END_HEREDOC:
						$stripStr .= "THINK;\n";
						for ($k = $i + 1; $k < $j; $k++) {
							if (is_string($tokens[$k]) && $tokens[$k] == ';') {
								$i = $k;
								break;
							} else if ($tokens[$k][0] == T_CLOSE_TAG) {
								break;
							}
						}
						break;
					default:
						$last_space = false;
						$stripStr .= $tokens[$i][1];
						break;
				}
			}
		}
		// 去掉多余换行
		$stripStr = preg_replace('/(\\r\\n)+/', "\r\n", $stripStr);
		return $stripStr;
	}
}

if (!function_exists('is_json')) {

	/**
	 * 判断字符串是否为json
	 *
	 * @author lxp 20180226
	 * @param string $string
	 * @return bool
	 */
	function is_json($string)
	{
		json_decode($string);
		return (json_last_error() == JSON_ERROR_NONE);
	}
}

if (!function_exists('time_format')) {

	/**
	 * 时长格式化
	 *
	 * @author lxp 20180429
	 * @param $seconds
	 * @param string $delimiter_h
	 * @param string $delimiter_i
	 * @param string $delimiter_s
	 * @return string
	 */
	function time_format($seconds, $delimiter_h = '小时', $delimiter_i = '分', $delimiter_s = '秒')
	{
		$hour = 0;
		$minute = 0;
		$second = 0;
		if ($seconds > 0) {
			$hour = floor($seconds / 3600);
			$minute = floor(($seconds - 3600 * $hour) / 60);
			$second = floor((($seconds - 3600 * $hour) - 60 * $minute) % 60);
		}
		$result = '';
		if ($hour) {
			$result = $hour . $delimiter_h;
		}
		if ($minute) {
			$result .= $minute . $delimiter_i;
		}
		$result .= $second . $delimiter_s;
		return $result;
	}
}

if (!function_exists('sql_bind')) {

	/**
	 * 组合SQL语句和参数
	 *
	 * @author lxp 20180710
	 * @param string $sql
	 * @param array $bindings
	 * @param string $sign
	 * @return string
	 */
	function sql_bind($sql, $bindings = [], $sign = '\?')
	{
		foreach ($bindings as $replace) {
			if (!is_null($replace)) {
				$replace = is_numeric($replace) ? $replace : "'" . $replace . "'";
			} else {
				$replace = 'null';
			}
			$sql = preg_replace('/' . $sign . '/', $replace, $sql, 1);
		}
		return $sql;
	}
}

if (!function_exists('request_async')) {

	/**
	 * 异步请求方法
	 *
	 * @author lxp 20180720
	 * @param string $url
	 * @param array $post_data
	 * @return bool
	 */
	function request_async($url, $post_data = [])
	{
		$urlinfo = parse_url($url);
		$host = $urlinfo['host'];
		$port = isset($urlinfo['port']) ? $urlinfo['port'] : 80;
		$path = isset($urlinfo['path']) ? $urlinfo['path'] : '';
		if ($path) {
			$query = isset($urlinfo['query']) ? '?' . $urlinfo['query'] : '';
			$path .= $query;
		}

		$fp = fsockopen($host, $port, $errno, $errstr, 10);
		if (!$fp) {
			return false;
		}

		$method = "GET";
		if (!empty($post_data)) {
			$method = "POST";
		}

		$header = "{$method} {$path} HTTP/1.1\r\n";
		$header .= "Host: {$host}\r\n";
		if (!empty($post_data)) {
			$post = [];
			foreach ($post_data as $k => $v) {
				$post[] = $k . "=" . urlencode($v);
			}
			$post = implode('&', $post);
			$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$header .= "Content-Length: " . strlen($post) . "\r\n";
			$header .= "Connection: Close\r\n\r\n";
			$header .= $post;
		} else {
			$header .= "Connection: Close\r\n\r\n";
		}
		fwrite($fp, $header);
		fclose($fp);
	}
}

if (!function_exists('client_real_ip')) {

	/**
	 * 获取客户端IP
	 * 需根据项目实际环境改写
	 *
	 * @author lxp 20181026
	 * @return array|string
	 */
	function client_real_ip()
	{
		$ip_temp = request()->server(env('client_real_ip','HTTP_X_FORWARDED_FOR'), request()->ip());

		$client_real_ip = '';
		preg_match_all('/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/', $ip_temp, $matches);
		if (isset($matches[0][0])) {
			$client_real_ip = $matches[0][0];
		}

		return $client_real_ip;
	}
}

if (!function_exists('xml_append')) {

	/**
	 * 添加xml节点
	 *
	 * @author lxp 20181128
	 * @param DOMDocument $dom DOM类
	 * @param DOMElement $parentNode 父节点对象
	 * @param string $childName 子节点名称
	 * @param string $childText 子节点值，可为空
	 * @return DOMElement 子节点对象
	 */
	function xml_append(DOMDocument $dom, &$parentNode, $childName, $childText = '')
	{
		$childNode = $dom->createElement($childName);
		if ($childText) {
			$childNode->appendChild($dom->createTextNode($childText));
		}
		$parentNode->appendChild($childNode);
		return $childNode;
	}
}