<?php
namespace App\Models;
use App\Notifications\NoticeFeedback;
use Illuminate\Notifications\Notifiable;

/**
 * 意见反馈模型
 *
 * @author ljy 20170904
 */
class Feedback extends BaseMdl {
	protected $table = 'feedback';
	protected $primaryKey = 'id';
	public $timestamps = true;
	// 不可被批量赋值的属性，反之其他的字段都可被批量赋值
	protected $guarded = [
		'id'
	];
}
