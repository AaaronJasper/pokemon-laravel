<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('pokemon', function (Blueprint $table) {
            // 添加一个名为 skill 的可空字符串列
            $table->string('skill1')->nullable()->default(null);
            $table->string('skill2')->nullable()->default(null);
            $table->string('skill3')->nullable()->default(null);
            $table->string('skill4')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pokemon', function (Blueprint $table) {
            // 如果需要回滚操作，可以在这里定义
            $table->dropColumn('skill1');
            $table->dropColumn('skill2');
            $table->dropColumn('skill3');
            $table->dropColumn('skill4');
        });
    }
};
