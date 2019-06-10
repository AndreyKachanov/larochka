<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

//@codingStandardsIgnoreLine
class RemoveEmailUniqueKeyFromJiraUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('jira_users', function (Blueprint $table) {
            //проверка на существование уникального ключа
            //если ключ есть - удаляем его
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('jira_users');

            if (array_key_exists('jira_users_email_unique', $indexesFound)) {
                $table->dropUnique('jira_users_email_unique');
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

            //проверка на существование уникального ключа
            //если ключа нет - создаем его
            $sm = Schema::getConnection()->getDoctrineSchemaManager();
            $indexesFound = $sm->listTableIndexes('jira_users');

            if (!array_key_exists('jira_users_email_unique', $indexesFound)) {
                $table->unique('email', 'jira_users_email_unique');
            }
        });
    }
}
