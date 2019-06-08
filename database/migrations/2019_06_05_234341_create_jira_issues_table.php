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
                $table->bigIncrements('id');
                $table->unsignedBigInteger('jira_id');
                $table->string('key')->nullable();
                $table->string('summary')->nullable();
                $table->string('issue_type')->nullable();
                $table->string('creator')->nullable();
                $table->string('assignee')->nullable();
                $table->string('status')->nullable();
                $table->timestamp('created_at')->nullable();
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
        if (Schema::hasTable('jira_issues')) {
            Schema::dropIfExists('jira_issues');
        }
    }
}
