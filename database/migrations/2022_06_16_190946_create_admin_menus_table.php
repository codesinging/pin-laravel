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
        Schema::create('admin_menus', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('page_id')->nullable()->comment('对应的页面ID');
            $table->string('name')->comment('菜单名称');
            $table->string('icon')->nullable()->comment('图标');
            $table->unsignedBigInteger('sort')->default(0)->comment('排列序号');
            $table->boolean('default')->default(false)->comment('是否选中');
            $table->boolean('opened')->default(false)->comment('是否展开');
            $table->boolean('status')->default(true)->comment('状态');

            $table->nestedSet();

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
        Schema::dropIfExists('admin_menus');
    }
};
