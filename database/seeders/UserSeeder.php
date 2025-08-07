<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('116_users')->insert([
            'name' => 'Phan Xuân Thọ',
            'email' => 'pxtho@ttn.edu.vn',
            'password' => Hash::make('tnu123456'), // Mật khẩu là 'password'
            'role' => 'admin',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
