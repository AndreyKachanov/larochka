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
            $table->index(['creator'], 'fk_creator_idx');
            $table->index(['assignee'], 'fk_assignee_idx');
            $table->index(['sender'], 'fk_sender_idx');

            //создаем 2 внешних ключа
            $table->foreign('creator', 'fk_jira_issues_creator')
                ->references('user_key')
                ->on('jira_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('assignee', 'fk_jira_issues_assignee')
                ->references('user_key')
                ->on('jira_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->foreign('sender', 'fk_jira_issues_sender')
                ->references('user_key')
                ->on('jira_users')
                ->onDelete('cascade')
                ->onUpdate('cascade');
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
            $table->dropForeign('fk_jira_issues_sender');
            //удаляем 2 индекса
            $table->dropIndex('fk_creator_idx');
            $table->dropIndex('fk_assignee_idx');
            $table->dropIndex('fk_sender_idx');
        });
    }
}
