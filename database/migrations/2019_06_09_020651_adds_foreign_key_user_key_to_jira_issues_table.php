<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class AddsForeignKeyUserKeyToJiraIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_issues', function (Blueprint $table) {

            //создаем 2 индекса
            $table->index(["creator"], 'fk_creator_idx');
            $table->index(["assignee"], 'fk_assignee_idx');

            //создаем 2 внешних ключа
            $table->foreign('creator', 'fk_jira_issues_creator')
                ->references('user_key')
                ->on('jira_users')
                ->onDelete('restrict')
                ->onUpdate('restrict');

            $table->foreign('assignee', 'fk_jira_issues_assignee')
                ->references('user_key')
                ->on('jira_users')
                ->onDelete('restrict')
                ->onUpdate('restrict');
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
            //удаляем 2 внешних ключа
            $table->dropForeign('fk_jira_issues_creator');
            $table->dropForeign('fk_jira_issues_assignee');
            //удаляем 2 индекса
            $table->dropIndex('fk_creator_idx');
            $table->dropIndex('fk_assignee_idx');
        });
    }
}
