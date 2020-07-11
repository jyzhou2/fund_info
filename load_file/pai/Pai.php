<?php

namespace App\Models;

/**
 * 随手拍模型
 *
 * @author yyj 20171113
 */
class Pai extends BaseMdl
{
	protected $table = 'pai';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
}
