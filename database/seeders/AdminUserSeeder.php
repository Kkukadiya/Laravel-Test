<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // remove all admins
        User::query()->truncate();

        // Add default admin users
        User::create([
            'email' => 'demo@gmail.com',
            'role_id' => 1,
            'name' => "Khanjan",
            'user_name' => "Khanjan",
            'password' => 'admin1234',
            'verification_token' => NULL,
            'verification_code' => NULL,
            'email_verified_at' => Carbon::now(),
            'avatar' => NULL,
            'remember_token' => NULL,
            "created_at" => Carbon::now(),
            "updated_at" => Carbon::now(),
        ]);
    }
}
