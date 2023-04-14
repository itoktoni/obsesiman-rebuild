<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {


        \DB::table('users')->delete();

        \DB::table('users')->insert(array (
            0 =>
            array (
                'id' => 2,
                'name' => 'user',
                'username' => 'user',
                'phone' => '08561117174',
                'email' => NULL,
                'email_verified_at' => '2022-11-13 21:15:52',
                'password' => '$2y$10$mWjzgjGlf.WbtEzwKCmXuOrn/Tv0D18A1SKyjYGKON1gzyxsg2QIi',
                'role' => 'user',
                'level' => 1,
                'active' => 1,
                'remember_token' => NULL,
                'created_at' => NULL,
                'updated_at' => '2022-11-14 00:00:25',
            ),
            1 =>
            array (
                'id' => 101,
                'name' => 'Admin',
                'username' => 'admin',
                'phone' => NULL,
                'email' => 'admin@gmail.com',
                'email_verified_at' => '2022-12-13 18:51:38',
                'password' => '$2y$10$xrnGLo0jr6Og5QDW4QwHj.RZn4iQU3/zjtz2V3iAOL3H.S3HJekgu',
                'role' => 'admin',
                'level' => 100,
                'active' => 1,
                'remember_token' => NULL,
                'created_at' => NULL,
                'updated_at' => '2023-02-25 10:54:19',
            ),
        ));


    }
}