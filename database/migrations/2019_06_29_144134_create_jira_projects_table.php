<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class CreateJiraProjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_projects', function (Blueprint $table) {
            $table->primary('project_id');

            $table->unsignedBigInteger('project_id');
            $table->string('key', 255)->nullable();
            $table->string('name', 255)->nullable();
            $table->string('avatar_url', 255)->nullable();

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
        Schema::dropIfExists('jira_projects');
    }
}
