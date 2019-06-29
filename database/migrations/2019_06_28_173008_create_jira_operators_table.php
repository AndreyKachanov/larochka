<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJiraOperatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('jira_operators')) {
            Schema::create('jira_operators', function (Blueprint $table) {
                $table->primary('user_key');
                $table->string('user_key', 255)->unique();
                $table->unsignedSmallInteger('line_id');
                $table->timestamps();

                //создаем  индекс для line_id
                $table->index(['line_id'], 'fk_jira_operators_jira_operator_lines_line_id_idx');
                //$table->index(['user_key'], 'fk_jira_operators_jira_creators_user_key_idx');

                //создаем внешний ключ для user_key поля
                $table->foreign('user_key', 'fk_jira_operators_jira_creators_user_key')
                    ->references('user_key')
                    ->on('jira_creators')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');

                //создаем внешних ключа для line_id поля
                $table->foreign('line_id', 'fk_jira_operators_jira_operator_lines_line_id')
                    ->references('line_id')
                    ->on('jira_operator_lines')
                    ->onDelete('cascade')
                    ->onUpdate('cascade');
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
        Schema::dropIfExists('jira_operators');
    }
}
