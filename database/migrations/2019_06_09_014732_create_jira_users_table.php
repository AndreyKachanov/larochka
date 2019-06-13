<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class CreateJiraUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('jira_users')) {
            Schema::create('jira_users', function (Blueprint $table) {

                $table->primary('user_key');

                $table->string('user_key', 255)->unique();
                $table->string('display_name', 255)->nullable();
                $table->string('email', 255)->nullable();
                $table->string('avatar', 255)->nullable();
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
        Schema::dropIfExists('jira_users');
    }
}
