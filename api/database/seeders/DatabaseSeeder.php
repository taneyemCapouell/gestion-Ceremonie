<?php

namespace Database\Seeders;

use Database\Seeders\RolesTableSeeder as SeedersRolesTableSeeder;
use Illuminate\Database\Seeder;
use RolesTableSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
         $this->call(SeedersRolesTableSeeder::class);
         $this->call(UsersTableSeeder::class);

    }
}
