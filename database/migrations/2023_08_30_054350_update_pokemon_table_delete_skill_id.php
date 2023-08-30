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
            if (Schema::hasColumn('pokemon', 'skill1_id')) {
                $table->dropColumn('skill1_id');
            }
            if (Schema::hasColumn('pokemon', 'skill2_id')) {
                $table->dropColumn('skill2_id');
            }
            if (Schema::hasColumn('pokemon', 'skill3_id')) {
                $table->dropColumn('skill3_id');
            }
            if (Schema::hasColumn('pokemon', 'skill4_id')) {
                $table->dropColumn('skill4_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
