<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SystemGroupConnectionRoleTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('system_group_connection_role')->delete();
        
        \DB::table('system_group_connection_role')->insert(array (
            0 => 
            array (
                'system_role_code' => 'admin',
                'system_group_code' => 'aplikasi',
            ),
            1 => 
            array (
                'system_role_code' => 'admin',
                'system_group_code' => 'master',
            ),
            2 => 
            array (
                'system_role_code' => 'admin',
                'system_group_code' => 'setting',
            ),
        ));
        
        
    }
}