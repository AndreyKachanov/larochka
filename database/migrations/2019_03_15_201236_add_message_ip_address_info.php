<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddMessageIpAddressInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('messages', function (Blueprint $table) {

            if (!Schema::hasColumns('messages', [
                'city',
                'country_code',
                'isp',
                'mobile',
                'org',
                'region_name'
            ])) {
                $table->string('city', 255)->nullable()->after('ip_address');
                $table->string('country_code', 255)->nullable()->after('city');
                $table->string('isp', 255)->nullable()->after('country_code');
                $table->boolean('mobile')->nullable()->after('isp');
                $table->string('org', 255)->nullable()->after('mobile');
                $table->string('region_name', 255)->nullable()->after('org');
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
        Schema::table('messages', function (Blueprint $table) {

            if (Schema::hasColumns('messages', [
                'city',
                'country_code',
                'isp',
                'mobile',
                'org',
                'region_name'
            ])) {
                $table->dropColumn('city');
                $table->dropColumn('country_code');
                $table->dropColumn('isp');
                $table->dropColumn('mobile');
                $table->dropColumn('org');
                $table->dropColumn('region_name');
            }
        });
    }
}
