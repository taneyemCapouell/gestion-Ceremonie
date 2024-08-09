<?php

namespace Database\Seeders;

use App\Models\Event_type;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EventTypeTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Event_type::create(['name' => 'mariage']);
        Event_type::create(['name' => 'anniversaire']);
        Event_type::create(['name' => 'batême']);
        Event_type::create(['name' => 'funérail']);
        Event_type::create(['name' => 'fête de travail']);
        Event_type::create(['name' => 'réceptions d\'entreprise']);
        Event_type::create(['name' => 'lancements de produits']);
    }
}
