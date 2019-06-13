<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJiraTransferredToLine2Table extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jira_transferred_to_line2', function (Blueprint $table) {

            //составной первичный ключ, чтобы не дублировались записи
            $table->primary(['user_key', 'issue_id']);

            $table->string('user_key', 255);
            $table->unsignedBigInteger('issue_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jira_transferred_to_line2');
    }
}
