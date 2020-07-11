<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateAutonumListTable extends Migration
{
	private $tableName = 'autonum_list';
	private $tableComment = '蓝牙号多对多设置';
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
			$table->integer('autonum')->comment('蓝牙编号');
			$table->string('mx_and',5)->comment('安卓门限')->default('-68');
			$table->string('mx_dlj',5)->comment('导览机门限')->default('-68');
			$table->string('mx_ios', 5)->comment('ios门限')->default(10);
			$table->text('exhibit_list')->comment('关联展品id列表');
			$table->text('exhibit_name')->comment('展品名称');
			$table->integer('x')->comment('新x坐标')->default(0);
			$table->integer('y')->comment('新y坐标')->default(0);
			$table->integer('map_id')->comment('所在地图id')->default(0);
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

