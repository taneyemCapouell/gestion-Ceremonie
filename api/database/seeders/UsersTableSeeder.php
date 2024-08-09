<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Ramsey\Uuid\Uuid;

class  UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()


    {
        // Créer trois exemplaires d'utilisateurs avec des rôles différents
        $adminRole = Role::where('name', 'admin')->first();
        $eventRole = Role::where('name', 'évènementiel')->first();
        $controllerRole = Role::where('name', 'contrôleur')->first();

        // Création des utilisateurs
        User::create([
            'id' => Uuid::uuid4(),
            'firstname' => 'taneyem',
            'lastname' => 'capouell',
            'email' => 'taneyemc@gmail.com',
            'phone' => '679417229',
            'gender' => 'masculin',
            'role_id' => $adminRole->id,
            'password' => Hash::make('password'), // Vous pouvez utiliser bcrypt('password') également
        ]);

         User::create([
            'id' => Uuid::uuid4(),
            'firstname' => 'teneyem',
            'lastname' => 'desto',
            'email' => 'teneyemdesto@gmail.com',
            'phone' => '679353205',
            'gender' => 'masculin',
            'role_id' =>  $eventRole->id,
            'password' => Hash::make('password'), // Vous pouvez utiliser bcrypt('password') également
        ]);

        User::create([
            'id' => Uuid::uuid4(),
            'firstname' => 'apong',
            'lastname' => 'marley',
            'email' => 'marleyapong@gmail.com',
            'phone' => '676524895',
            'gender' => 'masculin',
            'password' => Hash::make('password'), // Vous pouvez utiliser bcrypt('password') également
            'role_id' =>  $controllerRole->id,
        ]);

        // $admin->roles()->attach([1,2]);
        // $evennementiel->roles()->attach([1,2]);
        // $controlleur->roles()->attach([1,2]);
    }

    // public function run()
    // {
    //     // Utilisation de Faker pour générer des données aléatoires
    //     $faker = Faker::create();

    //     // Création de 10 utilisateurs de test
    //     for ($i = 0; $i <5; $i++) {
    //         DB::table('users')->insert([
    //             'id' => Uuid::uuid4(),
    //             'firstname' => "taneyem",
    //             'lastname' => "capouell",
    //             'email' => $faker->unique()->safeEmail,
    //             'phone' => $faker->phoneNumber,
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //             'role_id' => Uuid::uuid4()->toString(),
    //             'gender' => $faker->randomElement(['homme', 'femme']),
    //             'password' => Hash::make('password'), // Vous pouvez utiliser bcrypt('password') également
    //         ]);
    //     }
    // }
}
