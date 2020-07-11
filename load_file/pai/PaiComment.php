<?php

namespace App\Models;

/**
 * 随手拍评论模型
 *
 * @author yyj 20171113
 */
class PaiComment extends BaseMdl
{
	protected $table = 'pai_comment';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
}
