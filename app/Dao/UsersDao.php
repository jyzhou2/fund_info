<?php

namespace App\Dao;

use App\Models\Users;

/**
 * 用户业务模型
 *
 * @author lxp 20170826
 */
class UsersDao extends Users
{

	/**
	 * 生成随机昵称
	 *
	 * @author lxp 20170909
	 * @param string $nickname_base
	 * @return string
	 */
	public static function get_nickname($nickname_base = '')
	{
		$str = mt_rand(10000000, 99999999);
		$md5str = md5($str . date('YmdHis'));
		if ($nickname_base) {
			$nickname = $nickname_base . substr($md5str, 6, 10);
		} else {
			$nickname = 'U' . substr($md5str, 10, 10);
		}
		if (Users::where('nickname', $nickname)->count('uid') > 0) {
			return self::get_nickname($nickname_base);
		}
		return $nickname;
	}

	/**
	 * 生成随机用户名
	 *
	 * @author lxp 20181109
	 * @param string $username_base
	 * @return string
	 */
	public static function get_username($username_base = '')
	{
		$str = mt_rand(10000000, 99999999);
		$md5str = md5($str . date('YmdHis'));
		if ($username_base) {
			$username = $username_base . substr($md5str, 6, 10);
		} else {
			$username = 'U' . substr($md5str, 10, 10);
		}
		if (Users::where('username', $username)->count('uid') > 0) {
			return self::get_nickname($username_base);
		}
		return $username;
	}
}
