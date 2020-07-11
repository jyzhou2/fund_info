<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Autonum extends Model
{
	protected $table = 'autonum_list';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
}
