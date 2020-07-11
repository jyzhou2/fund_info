<?php
namespace App\Models;

/**
 * 问卷模型
 *
 * @author yyj 20180706
 */
class Queslist extends BaseMdl
{
	protected $table = 'queslist';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
	
}
