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
        Schema::table('system_menu_connection_link', function (Blueprint $table) {
            $table->foreign(['system_link_code'], 'system_menu_connection_link_ibfk_2')->references(['system_link_code'])->on('system_link')->onUpdate('CASCADE')->onDelete('CASCADE');
            $table->foreign(['system_menu_code'], 'system_menu_connection_link_ibfk_1')->references(['system_menu_code'])->on('system_menu')->onUpdate('CASCADE')->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('system_menu_connection_link', function (Blueprint $table) {
            $table->dropForeign('system_menu_connection_link_ibfk_2');
            $table->dropForeign('system_menu_connection_link_ibfk_1');
        });
    }
};
