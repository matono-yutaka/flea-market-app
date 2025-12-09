<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            [
                'id' => 1,
                'name' => 'ユーザー1',
                'email' => 'test1@example.com',
                'password' => bcrypt('password1'),
            ],[
                'id' => 2,
                'name' => 'ユーザー2',
                'email' => 'test2@example.com',
                'password' => bcrypt('password2'),
            ],[
                'id' => 3,
                'name' => 'ユーザー3',
                'email' => 'test3@example.com',
                'password' => bcrypt('password3'),
            ]
        ]);
    }
}
