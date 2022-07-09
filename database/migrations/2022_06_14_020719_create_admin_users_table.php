<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin_users', function (Blueprint $table) {
            $table->id();

            $table->string('username')->unique()->comment('登录用户名');
            $table->string('name')->unique()->comment('管理员名称');
            $table->string('password')->comment('登录密码');
            $table->boolean('super')->default(false)->comment('是否超级管理员');
            $table->integer('login_count')->default(0)->comment('登录次数');
            $table->integer('login_error_count')->default(0)->comment('登录错误次数');
            $table->timestamp('last_login_time')->nullable()->comment('最后登录时间');
            $table->ipAddress('last_login_ip')->nullable()->comment('最后登录IP');
            $table->boolean('status')->default(true)->comment('状态');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin_users');
    }
};
