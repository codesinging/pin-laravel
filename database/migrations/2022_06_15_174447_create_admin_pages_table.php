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
        Schema::create('admin_pages', function (Blueprint $table) {
            $table->id();

            $table->string('name')->comment('页面名称');
            $table->string('path')->unique()->comment('页面路径');
            $table->unsignedBigInteger('sort')->default(0);
            $table->boolean('public')->default(false);
            $table->boolean('status')->default(true);

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
        Schema::dropIfExists('admin_pages');
    }
};
