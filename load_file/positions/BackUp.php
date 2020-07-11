<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BackUp extends Model
{
	protected $table = 'backup';
	protected $primaryKey = 'id';
	protected $connection = 'rent_mysql';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
}
