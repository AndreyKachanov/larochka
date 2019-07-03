<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class CreateJiraComponentIssueTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $tableName = 'jira_component_issue';
        Schema::create($tableName, function (Blueprint $table) use ($tableName) {
            $table->engine = 'InnoDB';

            //составной первичный ключ, чтобы не дублировались записи
            $table->primary(['issue_id', 'component_id']);

            $table->unsignedBigInteger('issue_id');
            $table->unsignedBigInteger('component_id');

            $table->index(['issue_id'], 'fk_' . $tableName . '_jira_issues_issue_id_idx');
            $table->index(['component_id'], 'fk_' . $tableName . '_jira_components_component_id_idx');

            //$table->foreign('component_id', 'fk_' . $tableName . '_jira_components_component_id')
            //    ->references('component_id')
            //    ->on('jira_components')
            //    ->onDelete('set null')
            //    ->onUpdate('no action');
            //
            //$table->foreign('issue_id', 'fk_'. $tableName .'_jira_issues_issue_id')
            //    ->references('issue_id')
            //    ->on('jira_issues')
            //    ->onDelete('set null')
            //    ->onUpdate('no action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jira_component_issue');
    }
}
