<?php
namespace App\Models;

/**
 * 分片上传文件碎片模型
 *
 * @author lxp 20160704
 */
class JiJinGusuan extends BaseMdl
{
    public $table='jijingusuan';

	protected $primaryKey = 'id';
	public $timestamps = false;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
}
