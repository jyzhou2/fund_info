<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateExhibitTable extends Migration
{
	private $tableName = 'exhibit';
	private $tableComment = '展品列表';
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
			$table->integer('is_show_map', false, true)->comment('是否在地图显示1显示2不显示')->default(1);
			$table->integer('is_show_list', false, true)->comment('是否在列表显示1显示2不显示')->default(1);
			$table->integer('look_num', false, true)->comment('浏览数量')->default(0);
			$table->integer('collection_num', false, true)->comment('收藏数量')->default(0);
			$table->integer('like_num', false, true)->comment('点赞数量')->default(0);
			$table->integer('comment_num', false, true)->comment('评论数量')->default(0);
			$table->integer('listen_num', false, true)->comment('收听数量')->default(0);
			$table->integer('map_id', false, true)->comment('地图id');
			$table->integer('x', false, true)->comment('x轴坐标');
			$table->integer('y', false, true)->comment('y轴坐标');
			$table->integer('exhibition_id', false, true)->comment('展厅id');
			$table->string('exhibit_num',10)->comment('展品编号');
			$table->text('exhibit_img')->comment('图片存储路径');
			$table->integer('imgs_num', false, true)->comment('展品详情图片数量')->default(0);
			$table->string('exhibit_name', 50)->comment('展品中文名称');
			$table->decimal('order_id',10,6)->default(100)->comment('排序编号');
			$table->integer('type', false, true)->comment('1常展2临展')->default(1);
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
