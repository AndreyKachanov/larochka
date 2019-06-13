<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class CreateJiraIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('jira_issues')) {
            Schema::create('jira_issues', function (Blueprint $table) {

                $table->primary('issue_id');

                $table->unsignedBigInteger('issue_id');
                $table->unsignedInteger('key')->nullable();
                $table->string('summary')->nullable();
                $table->string('issue_type')->nullable();
                $table->string('creator');
                $table->string('assignee')->nullable();
                $table->string('status')->nullable();
                $table->string('resolution')->nullable();
                $table->timestamp('created_in_jira')->nullable();
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
        Schema::dropIfExists('jira_issues');
    }
}
