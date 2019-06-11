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
        Schema::create('jira_component_issue', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            //составной первичный ключ, чтобы не дублировались записи
            $table->primary(['issue_id', 'component_jira_id']);

            $table->unsignedBigInteger('issue_id');
            $table->unsignedBigInteger('component_jira_id');

            $table->index(['issue_id'], 'fk_component_issue_jira_issues_id_idx');
            $table->index(['component_jira_id'], 'fk_component_issue_jira_components_jira_id_idx');

            $table->foreign('component_jira_id', 'fk_component_issue_jira_components_jira_id')
                ->references('jira_id')
                ->on('jira_components')
                ->onDelete('cascade')
                ->onUpdate('no action');

            $table->foreign('issue_id', 'fk_component_issue_jira_issues_id')
                ->references('id')
                ->on('jira_issues')
                ->onDelete('cascade')
                ->onUpdate('no action');
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
