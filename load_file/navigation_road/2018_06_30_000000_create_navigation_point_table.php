<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavigationPointTable extends Migration
{
	private $tableName = 'navigation_point';
	private $tableComment = '点位信息集合';
	private $primaryKey = 'id';
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function (Blueprint $table) {
			$table->increments($this->primaryKey)->unsigned()->comment('设备id');
			$table->string('auto_num',10)->nullable()->default('')->comment('点位编号');
			$table->integer('map_id', false,true)->comment('地图编号');
			$table->string('x',10)->nullable()->default('')->comment('x坐标');
			$table->string('y',10)->nullable()->default('')->comment('y坐标');
			$table->string('axis',100)->nullable()->default('')->comment('json格式点位信息');
			$table->string('datetime',11)->nullable()->default('')->comment('编辑时间');
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
        //
    }
}
