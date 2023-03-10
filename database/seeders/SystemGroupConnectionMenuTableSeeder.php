<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SystemGroupConnectionMenuTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('system_group_connection_menu')->delete();
        
        \DB::table('system_group_connection_menu')->insert(array (
            0 => 
            array (
                'system_group_code' => 'setting',
                'system_menu_code' => 'link',
            ),
            1 => 
            array (
                'system_group_code' => 'setting',
                'system_menu_code' => 'menu',
            ),
            2 => 
            array (
                'system_group_code' => 'setting',
                'system_menu_code' => 'permission',
            ),
            3 => 
            array (
                'system_group_code' => 'setting',
                'system_menu_code' => 'roles',
            ),
            4 => 
            array (
                'system_group_code' => 'setting',
                'system_menu_code' => 'setting',
            ),
            5 => 
            array (
                'system_group_code' => 'setting',
                'system_menu_code' => 'user',
            ),
        ));
        
        
    }
}