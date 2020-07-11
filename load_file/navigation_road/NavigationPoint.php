<?php
namespace App\Models;

/**
 * 地图点
 *
 * @author ljy 20170808
 */
class NavigationPoint extends BaseMdl
{
	protected $table = 'navigation_point';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
	
}
