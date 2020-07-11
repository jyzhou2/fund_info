<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateQuesinfoListTable extends Migration
{
    private $tableName = 'quesinfo_list';
    private $tableComment = '问卷调查题目';
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
            $table->integer('ques_id')->comment('问卷id')->nullable()->default(null);
            $table->string('question',500)->comment('题目标题')->nullable()->default(null);
            $table->dateTime('date_time')->comment('编辑时间')->nullable()->default(null);
            $table->integer('is_save')->comment('是否保存,1为保存')->default(0);
            $table->integer('type')->comment('题目类型,1为单选，2为多选，3为问答');
            $table->integer('quesinfo_id')->comment('题目编号');
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
