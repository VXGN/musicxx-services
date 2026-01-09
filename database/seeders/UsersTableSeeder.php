<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('123'),
            'role' => 'publisher',
        ]);
        DB::table('users')->insert([
            'name' => 'Regular User',
            'email' => 'regularc@example.com',
            'password' => bcrypt('123'),
            'role' => 'listener',
        ]);
    }
}
