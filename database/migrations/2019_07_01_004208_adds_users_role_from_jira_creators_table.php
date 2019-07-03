<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class AddsUsersRoleFromJiraCreatorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_users', function (Blueprint $table) {
            if (!Schema::hasColumn('jira_users', 'role_id')) {
                $table->unsignedSmallInteger('role_id')
                    ->nullable()
                    ->after('user_key');

                //создаем  индекс для role_id
                $table->index(['role_id'], 'fk_jira_users_jira_users_role_role_id_idx');

                //создаем внешний ключ для role_id поля
                $table->foreign('role_id', 'fk_jira_users_jira_users_role_role_id')
                    ->references('role_id')
                    ->on('jira_users_role')
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
        Schema::table('jira_users', function (Blueprint $table) {
            if (Schema::hasColumn('jira_users', 'role_id')) {
                $table->dropForeign('fk_jira_users_jira_users_role_role_id');
                $table->dropIndex('fk_jira_users_jira_users_role_role_id_idx');
                $table->dropColumn('role_id');
            }
        });
    }
}
