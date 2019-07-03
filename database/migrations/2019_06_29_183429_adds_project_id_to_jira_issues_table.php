<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class AddsProjectIdToJiraIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_issues', function (Blueprint $table) {
            if (!Schema::hasColumn('jira_issues', 'project_id')) {
                $table->unsignedBigInteger('project_id')->after('issue_id');

                //создаем  индекс для project_id
                $table->index(['project_id'], 'fk_jira_issues_jira_projects_project_id_idx');

                //создаем внешний ключ для project_id поля
                $table->foreign('project_id', 'fk_jira_issues_jira_projects_project_id')
                    ->references('project_id')
                    ->on('jira_projects')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('jira_issues', function (Blueprint $table) {
            if (Schema::hasColumn('jira_issues', 'project_id')) {
                $table->dropForeign('fk_jira_issues_jira_projects_project_id');
                $table->dropIndex('fk_jira_issues_jira_projects_project_id_idx');
                $table->dropColumn('project_id');
            }
        });
    }
}
