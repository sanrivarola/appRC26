<?php

namespace Database\Seeders;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Usuario admin
        $admin = User::firstOrCreate(
            ['email' => 'san@raracuenca.com'],
            [
                'name' => 'San Admin',
                'password' => Hash::make('REDACTED'),
            ]
        );
        $admin->assignRole('admin');

        // Usuario editor
        $editor = User::firstOrCreate(
            ['email' => 'ing.agronoma@raracuenca.com'],
            [
                'name' => 'Ing.Agronoma',
                'password' => Hash::make('REDACTED'),
            ]
        );
        $editor->assignRole('editor');

        // Usuario colaborador
        $colaborador = User::firstOrCreate(
            ['email' => 'ezequiel@raracuenca.com'],
            [
                'name' => 'Ezequiel',
                'password' => Hash::make('REDACTED'),
            ]
        );
        $colaborador->assignRole('colaborador');
    }
}
