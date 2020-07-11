<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateQuesinfoOptionTable extends Migration
{
    private $tableName = 'quesinfo_option';
    private $tableComment = '问卷题目选项';
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
            $table->integer('quesinfo_id')->comment('题目编号');
            $table->string('option_info',200)->comment('选项内容')->nullable()->default(null);
            $table->integer('option_num')->comment('选择票数');
            $table->integer('ques_id')->comment('问卷id');
            $table->integer('option_type')->comment('1为普通选项2为其他文本编辑');
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
