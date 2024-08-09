<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::create(['name' => 'admin', "value" => "1"] );
        Role::create(['name' => 'évènementiel',"value" => "2"]);
        Role::create(['name' => 'contrôleur',"value" => "3"]);
    }
}
