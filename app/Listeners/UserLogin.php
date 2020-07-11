<?php

namespace App\Listeners;

use App\Models\AdminLoginLog;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\Auth;

class UserLogin
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * handle
	 *
	 * @author lxp 20170615
	 * @param Login $event
	 */
	public function handle(Login $event)
	{
		$current_time = date('Y-m-d H:i:s');
		$current_ip = client_real_ip();

		// 可区分前后台
		switch (Auth::getDefaultDriver()) {
			case 'web':
				break;
			case 'admin':
				// 记录管理员登录日志
				AdminLoginLog::insert([
					'uid' => $event->user->uid,
					'login_time' => $current_time,
					'login_ip' => $current_ip
				]);
				break;
		}

		// 更新最后登录时间和IP
		$event->user->last_login = $current_time;
		$event->user->lastloginip = $current_ip;
		$event->user->save();
	}
}
