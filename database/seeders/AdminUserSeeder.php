<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Evitar duplicados
        if (DB::table('users')->where('email', 'admin@growwise.com')->exists()) {
            $this->command->info('Admin ya existe — seeder omitido.');
            return;
        }

        DB::table('users')->insert([
            'nombre'             => 'Admin',
            'apellido'           => 'GrowWise',
            'email'              => 'admin@growwise.com',
            'role'               => 'admin',
            'email_verified_at'  => now(),
            'password'           => Hash::make('Admin1234'),
            'telefono'           => null,
            'avatar'             => 'https://ui-avatars.com/api/?name=Admin+GrowWise&background=1B5E20&color=fff&size=40',
            'remember_token'     => null,
            'created_at'         => now(),
            'updated_at'         => now(),
        ]);

        $this->command->info('✅ Usuario admin creado: admin@growwise.com / Admin1234');
    }
}
