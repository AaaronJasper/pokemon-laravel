<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->boolean('sender_is_read')->default(false);
            $table->boolean('receiver_is_read')->default(true);
        });
    }

    public function down()
    {
        Schema::table('trades', function (Blueprint $table) {
            $table->dropColumn(['sender_is_read', 'receiver_is_read']);
        });
    }

};
