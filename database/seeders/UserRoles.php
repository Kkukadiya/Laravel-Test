<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class UserRoles extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::insert([
            ['name' => 'admin'],
            ['name' => 'user'],
        ]);
    }
}
