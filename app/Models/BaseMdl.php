<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * 模型基类
 *
 * @author lxp 20170104
 * @package App\Models
 */
class BaseMdl extends Model
{

	// 关闭Eloquent默认的 updated_at、created_at 两个字段
	public $timestamps = false;

	/**
	 * 覆写取当前模型表名称
	 * 例：类名称 CommonUserInfo 转化为表名称 common_user_info
	 *
	 * @author lxp 20170104
	 * @return mixed|string
	 */
	public function getTable()
	{
		if (isset($this->table)) {
			return $this->table;
		}

		return str_replace('\\', '', Str::snake((class_basename($this))));
	}

	/**
	 * 设置事务隔离级别
	 *
	 * @author lxp 20180530
	 * @param string $level 默认为提交读/不可重复读
	 * @param string $range 默认的修改范围为当前会话
	 * @throws \Exception
	 */
	public static function setTransactionIsolationLevel($level = 'READ COMMITTED', $range = 'SESSION')
	{
		if (!in_array($level, [
				'READ UNCOMMITTED',
				'READ COMMITTED',
				'REPEATABLE READ',
				'SERIALIZABLE'
			]) || !in_array($range, [
				'SESSION',
				'GLOBAL'
			])) {
			throw new \Exception('error param');
		}

		DB::statement("SET {$range} TRANSACTION ISOLATION LEVEL {$level}");
	}
}
