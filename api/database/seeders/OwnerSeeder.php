<?php

namespace Database\Seeders;

use App\Models\Owner;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class OwnerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Création des propietaires d'evennements
        Owner::create([
            'firstname' => 'kuete',
            'lastname' => 'fresnel',
            'email' => 'kuetefresnel@gmail.com',
            'phone' => '679437229',
            'gender' => 'masculin',
            'adresse' => 'makepe missoke',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Tristique magna sit amet purus gravida quis',
        ]);
        
        Owner::create([
            'firstname' => 'lonmene',
            'lastname' => 'derlin',
            'email' => 'lonmenederlin@gmail.com',
            'phone' => '679437220',
            'gender' => 'masculin',
            'adresse' => 'chelem penja',
            'description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, tempor incididunt ut labore et dolore magna aliqua. Tristique magna sit amet purus gravida quis',
        ]);
    }
}
