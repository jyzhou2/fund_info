<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateSvgMapTable extends Migration
{
	private $tableName = 'svgmap';
	private $tableComment = '地图列表';
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
			$table->integer('floor_id', false, true)->comment('楼层id');
			$table->integer('width', false, true)->comment('地图尺寸宽');
			$table->integer('height', false, true)->comment('地图尺寸高');
			$table->decimal('map_size',10,4)->default(0)->comment('地图比例px/m');
			$table->decimal('map_angle',6,3)->default(0)->comment('手机朝向地图y轴正方向时与正北方向的夹角');
			$table->string('map_path', 255)->comment('地图存储路径');
			$table->string('png_map_path', 255)->nullable()->comment('PNG地图存储路径');
			$table->string('map_name', 20)->comment('地图名称');
			$table->text('map_name_json')->comment('多语种数据');
			$table->longText('route_point_list')->nullable()->comment('该地图的路线的点列表');
			$table->longText('route_line_list')->nullable()->comment('该地图的路线的线列表');
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
