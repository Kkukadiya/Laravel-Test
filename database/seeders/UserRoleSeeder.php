<?php

namespace Database\Seeders;

use App\Models\UserRole;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // remove all records
        UserRole::query()->truncate();

        // Add default roles
        $roles = [
            [
                "name" => 'admin',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
            [
                "name" => 'user',
                "created_at" => Carbon::now(),
                "updated_at" => Carbon::now(),
            ],
        ];
        UserRole::insert($roles);
    }
}
