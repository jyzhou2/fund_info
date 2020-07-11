<?php
namespace App\Models;

/**
 * 地图线
 *
 * @author ljy 20170808
 */
class NavigationRoad extends BaseMdl
{
	protected $table = 'navigation_road';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
	
}
