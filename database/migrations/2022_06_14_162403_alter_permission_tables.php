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
        Schema::table(config('permission.table_names.roles'), function (Blueprint $table){
            $table->after('guard_name', function (Blueprint $table){
                $table->string('description')->nullable()->comment('角色描述');
                $table->unsignedBigInteger('sort')->default(0)->comment('排列序号');
                $table->boolean('status')->default(true)->comment('角色状态');
            });
        });

        Schema::table(config('permission.table_names.permissions'), function (Blueprint $table){
            $table->after('guard_name', function (Blueprint $table){
                $table->unsignedBigInteger('permissionable_id')->nullable()->comment('关联模型ID');
                $table->string('permissionable_type')->nullable()->comment('关联模型类型');
            });
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table(config('permission.table_names.roles'), function (Blueprint $table){
            $table->dropColumn(['description', 'sort', 'status']);
        });

        Schema::table(config('permission.table_names.permissions'), function (Blueprint $table){
            $table->dropColumn(['permissionable_id', 'permissionable_type']);
        });
    }
};
