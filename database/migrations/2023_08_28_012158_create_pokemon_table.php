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
        Schema::create('pokemon', function (Blueprint $table) {
            $table->id();
            $table->string("name");
            $table->integer("level");
            $table->string("race");
            $table->integer("nature_id");
            $table->integer("ability_id");
            $table->integer("skill1_id")->default(0);
            $table->integer("skill2_id")->default(0);
            $table->integer("skill3_id")->default(0);
            $table->integer("skill4_id")->default(0);
            $table->boolean("status")->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pokemon');
    }
};
