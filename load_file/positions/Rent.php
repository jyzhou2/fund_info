<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rent extends Model
{
	protected $table = 'rent';
	protected $primaryKey = 'RENT_ID';
	protected $connection = 'rent_mysql';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'RENT_ID'
	];
}
