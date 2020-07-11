<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class BeforeMiddleware
{
	/**
	 * 前置中间件，运行于处理请求之前
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \Closure $next
	 * @return mixed
	 */
	public function handle($request, Closure $next)
	{

		/**
		 * 监听所有执行的sql，记录日志
		 * 过滤查询语句
		 *
		 * @author lxp 20170111
		 */
		DB::listen(function ($query) {
			if (preg_match('/^(insert|update|delete|replace)/i', $query->sql)) {
				// 后台操作独立记录
				$filename = 'sql';
				if (Auth::getDefaultDriver() == 'admin') {
					$filename = 'sql_admin';
				}

				// 取出当前访问的控制器及方法
				$actionAllName = Route::getCurrentRoute()->getActionName();
				$actionName = str_replace('App\\Http\\Controllers\\Admin\\', '', $actionAllName);

				$logObj = app('logext');
				$logObj->init($filename);
				$logObj->logbuffer('sql', "[{$query->time}]" . sql_bind($query->sql, $query->bindings));
				$logObj->logbuffer('action_name', $actionName);
				if (Auth::check()) {
					$user = Auth::user();
					$logObj->logbuffer('uid', $user->uid);
					$logObj->logbuffer('username', $user->username);
				}
				$logObj->logend();
			}
		});

		return $next($request);
	}
}
