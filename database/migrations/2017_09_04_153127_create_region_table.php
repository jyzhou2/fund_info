<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateRegionTable extends Migration
{
	private $tableName = 'region';
	private $tableComment = '地区库';
	private $primaryKey = 'region_id';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function (Blueprint $table) {
			$table->increments($this->primaryKey);
			$table->string('region_name', 100)->comment('地区名称');
			$table->unsignedInteger('parent_id')->comment('上级地区id')->default(0);
			$table->unsignedTinyInteger('sort_order')->comment('地区显示顺序')->default(255);
			$table->unsignedTinyInteger(env('DB_CONNECTION') == 'oracle' ? 'LAYER' : 'layer')->comment('地区层级数')->default(0);

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
