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
        Schema::create('admin_actions', function (Blueprint $table) {
            $table->id();

            $table->string('controller')->comment('控制器');
            $table->string('controller_name')->comment('控制器名');
            $table->string('action')->comment('动作');
            $table->string('action_name')->comment('动作名');
            $table->boolean('public')->default(true)->comment('是否公共');

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
        Schema::dropIfExists('admin_actions');
    }
};
