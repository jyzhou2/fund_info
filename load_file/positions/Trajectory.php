<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trajectory extends Model
{
    protected $table = 'trajectory';
    protected $primaryKey = 'id';
    public $timestamps = true;
    // 不可被批量赋值的属性，反之其他的字段都可被批量赋值
    protected $guarded = [
        'id'
    ];
    public function getLookDateAttribute($value)
    {
        if (!is_null($value)) {
            $value = date('Y-m-d', strtotime($value));
        }
        return $value;
    }

}
