<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateExhibitionTable extends Migration
{
	private $tableName = 'exhibition';
	private $tableComment = '展厅列表';
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
			$table->integer('is_lb', false, true)->comment('首页是否轮播1轮播2不轮播')->default(2);
			$table->integer('type', false, true)->comment('展览类别1主题展览2临时展览')->default(1);
			$table->integer('floor_id', false, true)->comment('楼层id');
			$table->text('exhibition_img')->comment('展厅图片存储路径');
			$table->string('exhibition_name', 20)->comment('展厅中文名称');
			$table->string('near_exhibition', 255)->comment('附近展厅');
			$table->integer('is_show_list', false, true)->comment('是否在列表显示1显示2不显示')->default(1);
			$table->decimal('order_id',10,6)->default(100)->comment('排序编号');
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
