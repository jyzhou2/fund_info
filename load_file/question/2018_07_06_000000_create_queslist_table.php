<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateQueslistTable extends Migration
{
    private $tableName = 'queslist';
    private $tableComment = '问卷调查主表';
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
            $table->string('title', 100)->comment('问卷标题')->nullable()->default(null);
            $table->dateTime('date_time')->comment('创建时间')->nullable()->default(null);
            $table->string('user_login',30)->comment('操作人员')->nullable()->default(null);
            $table->integer('num')->comment('参与人数')->nullable()->default(null);
            $table->dateTime('start_time')->comment('开始时间')->nullable()->default(null);
            $table->dateTime('end_time')->comment('结束时间')->nullable()->default(null);
            $table->text('description')->comment('描述')->nullable()->default(null);
            $table->integer('language')->comment('语种信息')->default(1);
            $table->integer('status')->comment('是否正在进行,1为进行中')->default(0);
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
