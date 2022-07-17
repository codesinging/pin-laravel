<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting_options', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('group_id')->comment('配置分组ID');
            $table->string('name')->comment('配置名');
            $table->string('description')->nullable()->comment('配置描述');
            $table->string('key')->comment('配置键');
            $table->string('type')->comment('输入组件类型');
            $table->json('value')->nullable()->comment('配置默认值');
            $table->json('attributes')->nullable()->comment('配置输入组件属性');
            $table->json('data')->nullable()->comment('配置输入组件其它数据');
            $table->boolean('initial')->default(true)->comment('是否初始化配置值');
            $table->integer('sort')->default(0)->comment('排列序号');
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
        Schema::dropIfExists('setting_options');
    }
};
