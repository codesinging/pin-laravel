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
        Schema::create('admin_logs', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('user_id')->comment('操作用户ID');
            $table->string('method', 12)->comment('请求方法');
            $table->string('path')->nullable()->comment('请求路径');
            $table->ipAddress('ip')->nullable()->comment('请求IP地址');
            $table->json('input')->nullable()->comment('请求参数');
            $table->integer('status')->nullable()->comment('响应状态码');
            $table->integer('code')->nullable()->comment('响应内容代码');
            $table->string('message')->nullable()->comment('响应消息');
            $table->json('data')->nullable()->comment('响应数据');

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
        Schema::dropIfExists('admin_logs');
    }
};
