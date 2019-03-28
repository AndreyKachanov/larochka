<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class CreateVkPostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('vk_posts')) {
            Schema::create('vk_posts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('text');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('vk_posts')) {
            Schema::dropIfExists('vk_posts');
        }
    }
}
