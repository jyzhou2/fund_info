<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrajectoryListTable extends Migration
{
	private $tableName = 'trajectory_list';
	private $tableComment = '定位记录列表管理';
	private $primaryKey = 'id';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function (Blueprint $table) {
			$table->increments($this->primaryKey);
			$table->string('deviceno', 13)->comment('设备编号');
			$table->string('app_kind',1)->comment('设备类型,i:IOS,a:安卓,d:导览机,w:微信');
			$table->integer("dlj_type")->default(0)->comment('导览机类型1:十代机,2:欧7,3:K4');
			$table->string("card_id",60)->nullable()->comment('证件编号');
			$table->integer("rent_id")->default(0)->comment('租赁id');
			$table->timestamp('rent_time')->nullable()->comment('租赁开始时间');
			$table->timestamp('back_time')->nullable()->comment('租赁归还时间');
			$table->string("rent_name",20)->nullable()->comment('租赁者姓名');
			$table->integer("uid")->default(0)->comment('用户id');
			$table->integer("rent_type")->default(1)->comment('租赁类型1租赁中2已归还');
			$table->date("look_date")->comment('浏览日期');
			$table->timestamps();
			if (env('DB_CONNECTION') == 'oracle') {
				$table->comment = $this->tableComment;
			}
		});

		if (env('DB_CONNECTION') == 'mysql') {
			DB::statement("ALTER TABLE `" . DB::getTablePrefix() . $this->tableName . "` comment '{$this->tableComment}'");
		}
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::dropIfExists($this->tableName);
		if (env('DB_CONNECTION') == 'oracle') {
			$sequence = DB::getSequence();
			$sequence->drop(strtoupper($this->tableName . '_' . $this->primaryKey . '_SEQ'));
		}
	}
}
