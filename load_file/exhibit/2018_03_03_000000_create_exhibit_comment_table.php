<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateExhibitCommentTable extends Migration
{
	private $tableName = 'exhibit_comment';
	private $tableComment = '展品评论表';
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
			$table->integer('uid', false, true)->comment('用户id');
			$table->integer('exhibit_id', false, true)->comment('展品id');
			$table->integer('exhibition_id', false, true)->comment('展览id');
			$table->integer('type', false, true)->comment('类别1展厅2展品');
			$table->text('comment')->comment('评论内容')->nullable();
			$table->integer('is_check', false, true)->comment('类别1未审核2已审核');
			$table->integer('like_num', false, true)->comment('点赞数量');
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
