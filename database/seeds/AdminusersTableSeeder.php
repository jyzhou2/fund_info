<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class AdminusersTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		// 处理密码
		$salt = Str::random(6);
		$password = get_password('hengda', $salt);

		DB::table('admin_users')->insert([
			'groupid' => '1',
			'username' => env('ADMIN_USERNAME', 'hdroot'),
			'nickname' => '超级管理员',
			'salt' => $salt,
			'password' => $password,
			'created_at' => date('Y-m-d H:i:s')
		]);

		$salt_admin = Str::random(6);
		$password_admin = get_password('hengda', $salt_admin);
		DB::table('admin_users')->insert([
			'groupid' => '2',
			'username' => 'admin',
			'nickname' => '管理员',
			'salt' => $salt_admin,
			'password' => $password_admin,
			'created_at' => date('Y-m-d H:i:s')
		]);
	}
}
