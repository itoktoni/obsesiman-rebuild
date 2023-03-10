<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('system_group_connection_menu', function (Blueprint $table) {
            $table->foreign(['system_menu_code'], 'system_group_connection_menu_ibfk_2')->references(['system_menu_code'])->on('system_menu')->onUpdate('CASCADE');
            $table->foreign(['system_group_code'], 'system_group_connection_menu_ibfk_1')->references(['system_group_code'])->on('system_group')->onUpdate('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_group_connection_menu', function (Blueprint $table) {
            $table->dropForeign('system_group_connection_menu_ibfk_2');
            $table->dropForeign('system_group_connection_menu_ibfk_1');
        });
    }
};
