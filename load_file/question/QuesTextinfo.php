<?php
namespace App\Models;

/**
 * 问卷问答题答案模型
 *
 * @author yyj 20180706
 */
class QuesTextinfo extends BaseMdl
{
	protected $table = 'ques_textinfo';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
	
}
