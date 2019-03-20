<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddsNullableToMessagesTableColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('ip_address')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('sender')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('ip_address')->nullable(false)->change();
        });
    }
}
