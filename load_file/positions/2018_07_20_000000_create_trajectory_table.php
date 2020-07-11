<?php
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrajectoryTable extends Migration
{
    private $tableName = 'trajectory';
    private $tableComment = '轨迹记录管理';
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
			$table->integer("dlj_type")->comment('导览机类型1:十代机,2:欧7,3:K4');
            $table->integer("auto_num")->comment('蓝牙编号');
            $table->integer("map_id")->comment('地图编号');
            $table->integer("x")->comment('x坐标');
            $table->integer("y")->comment('y坐标');
            $table->integer("uid")->default(0)->comment('用户id');
			$table->integer("rent_id")->default(0)->comment('租赁id');
            $table->string("card_id",60)->default(0)->comment('证件编号');
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