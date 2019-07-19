<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class AddsTypeIdToJiraIssuesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_issues', function ($table) {
            if (Schema::hasColumn('jira_issues', 'issue_type')) {
                $table->dropColumn('issue_type');
            }

            if (!Schema::hasColumn('jira_issues', 'issue_type_id')) {
                $table->unsignedSmallInteger('issue_type_id')->after('summary')->nullable();
                $table->index(['issue_type_id'], 'fk_issue_type_id_idx');

                //создаем 2 внешних ключа
                $table->foreign('issue_type_id', 'fk_jira_iissue_type_id')
                    ->references('id')
                    ->on('jira_issue_types')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
            }

            //if (Schema::hasColumn('jira_issues', 'type_id')) {
            //    $table->index(['type_id'], 'fk_type_id_idx');
            //
            //    //создаем 2 внешних ключа
            //    $table->foreign('type_id', 'fk_jira_issues_type_id')
            //        ->references('id')
            //        ->on('jira_issue_types')
            //        ->onDelete('cascade')
            //        ->onUpdate('cascade');
            //}
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
            if (Schema::hasColumn('jira_issues', 'issue_type_id')) {
                $table->dropForeign('fk_jira_iissue_type_id');
                $table->dropIndex('fk_issue_type_id_idx');
                $table->dropColumn('issue_type_id');
            }
        });
    }
}
