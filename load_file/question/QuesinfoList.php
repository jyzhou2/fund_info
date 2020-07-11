<?php
namespace App\Models;

/**
 * 问卷题目模型
 *
 * @author yyj 20180706
 */
class QuesinfoList extends BaseMdl
{
	protected $table = 'quesinfo_list';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
	
}
