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
        Schema::create('admin_logins', function (Blueprint $table) {
            $table->id();

            $table->integer('user_id')->index()->comment('管理员ID');
            $table->dateTime('time')->nullable()->comment('登录时间');
            $table->ipAddress('ip')->nullable()->comment('登录IP');
            $table->boolean('result')->default(false)->comment('登录结果');
            $table->unsignedBigInteger('code')->comment('响应代码');
            $table->string('message')->nullable()->comment('响应信息');

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
        Schema::dropIfExists('admin_logins');
    }
};
