<?php
namespace App\Models;

/**
 * 数据库安装记录模型
 *
 * @author yyj 20180918
 */
class Migrations extends BaseMdl {
	protected $primaryKey = 'id';
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
}
