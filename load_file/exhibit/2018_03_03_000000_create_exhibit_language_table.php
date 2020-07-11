<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateExhibitLanguageTable extends Migration
{
	private $tableName = 'exhibit_language';
	private $tableComment = '展品语种数据表';
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
			$table->integer('exhibit_id', false, true)->comment('展品编号');
			$table->integer('language',false, true)->comment('语种类型1中文，2英语，3日语，4韩语，5法语，6西班牙语，7德语，8俄语，9意大利，10蒙语');
			$table->string('exhibit_name', 500)->comment('展品名称')->nullable();
			$table->string('audio', 255)->comment('语音地址')->nullable();
			$table->text('content')->comment('展厅简介')->nullable();
			$table->text('knowledge')->comment('科普知识')->nullable();
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
