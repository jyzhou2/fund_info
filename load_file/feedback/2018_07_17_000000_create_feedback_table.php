<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
class CreateFeedbackTable extends Migration
{
    private $tableName = 'feedback';
    private $tableComment = '意见反馈';
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
            $table->integer('feedback_uid')->comment('反馈用户ID')->nullable();
            $table->string('feedback_username')->comment('反馈用户')->nullable();
            $table->string('feedback_user_phone')->comment('反馈用户手机号')->nullable();
            $table->string('img')->comment('图片')->nullable();
            $table->text('feedback_content')->comment('反馈内容')->nullable();
            $table->dateTime('feedback_date_time')->comment('反馈时间')->nullable();
            $table->integer('is_read')->comment('是否阅读,1为已读,0未读')->default(1);
            $table->integer('reply_uid')->comment('回复操作员ID')->nullable()->default(0);
            $table->string('reply_username')->comment('回复操作员')->nullable()->default(null);
            $table->text('reply_content')->comment('回复内容')->nullable()->default(null);
            $table->dateTime('reply_datetime')->comment('回复时间')->nullable()->default(null);
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
