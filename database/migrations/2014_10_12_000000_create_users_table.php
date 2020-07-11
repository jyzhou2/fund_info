<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class CreateUsersTable extends Migration
{
	private $tableName = 'users';
	private $tableComment = '用户表';
	private $primaryKey = 'uid';

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create($this->tableName, function (Blueprint $table) {
			$table->increments(env('DB_CONNECTION') == 'oracle' ? strtoupper($this->primaryKey) : $this->primaryKey)->comment('用户id');
			$table->string('username', 100)->comment('用户名')->unique();
			$table->string('nickname', 100)->comment('昵称')->nullable();
			$table->text('avatar')->comment('头像')->nullable();
			$table->char(env('DB_CONNECTION') == 'oracle' ? 'PASSWORD' : 'password', 32)->comment('密码');
			$table->string('email', 100)->comment('用户邮箱')->nullable();
			$table->string('phone', 60)->comment('用户手机号')->nullable();
			$table->timestamp('last_login')->comment('最后登录时间')->nullable();
			$table->string('lastloginip', 15)->comment('最后登录IP')->nullable();
			$table->char('salt', 6)->comment('密码盐');
			$table->rememberToken();
			$table->char('api_token', 32)->comment('APP token')->nullable()->index();
			$table->char('plat', 10)->comment('注册平台来源')->nullable();
			$table->unsignedTinyInteger('is_test')->comment('测试用户标记位，1为测试用户')->default(0);
			$table->timestamp('token_expired')->comment('token过期时间')->nullable();
			$table->char('deviceno', 13)->comment('设备号')->nullable();
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
