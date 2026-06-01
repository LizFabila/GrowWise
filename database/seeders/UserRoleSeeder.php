<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserRoleSeeder extends Seeder
{
    public function run(): void
    {
        // Actualizar usuarios existentes
        $users = User::all();

        foreach ($users as $user) {
            // Por defecto, todos los usuarios existentes serán vendedores
            // (porque ya tienen acceso al dashboard)
            $user->role = 'vendedor';
            $user->save();
        }

        // Crear un usuario cliente de prueba
        User::create([
            'nombre' => 'Cliente',
            'apellido' => 'Prueba',
            'email' => 'cliente@test.com',
            'password' => Hash::make('12345678'),
            'role' => 'cliente',
            'avatar' => 'https://ui-avatars.com/api/?name=Cliente+Prueba&background=2E7D32&color=fff&size=40',
        ]);

        // Crear un usuario vendedor de prueba
        User::create([
            'nombre' => 'Vendedor',
            'apellido' => 'Prueba',
            'email' => 'vendedor@test.com',
            'password' => Hash::make('12345678'),
            'role' => 'vendedor',
            'avatar' => 'https://ui-avatars.com/api/?name=Vendedor+Prueba&background=2E7D32&color=fff&size=40',
        ]);

        $this->command->info('Usuarios actualizados correctamente:');
        $this->command->info('- Cliente: cliente@test.com / 12345678');
        $this->command->info('- Vendedor: vendedor@test.com / 12345678');
    }
}
