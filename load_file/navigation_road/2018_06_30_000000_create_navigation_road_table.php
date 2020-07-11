<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNavigationRoadTable extends Migration
{
	private $tableName = 'navigation_road';
	private $tableComment = '路径集合';
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
			$table->string('start_id',20)->nullable()->default('')->comment('开始节点');
			$table->string('end_id',20)->nullable()->default('')->comment('结束节点');
			$table->string('distance',20)->nullable()->default('')->comment('总距离');
			$table->string('axis',5000)->nullable()->default('')->comment('点位路径');
			$table->integer('floors', false,true)->comment('经过点位之间路径条数');
			$table->text('road')->nullable()->comment('json格式路径');
			$table->integer('map_id', false,true)->comment('map_id');
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
