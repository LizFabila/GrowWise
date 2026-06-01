<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MetodosPagoSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('metodos_pago')->insert([
            ['nombre' => 'Efectivo', 'descripcion' => 'Pago en efectivo al momento de la entrega', 'activo' => 1, 'created_at' => now()],
            ['nombre' => 'Tarjeta de crédito/débito', 'descripcion' => 'Pago con tarjeta VISA o Mastercard', 'activo' => 1, 'created_at' => now()],
            ['nombre' => 'Transferencia bancaria', 'descripcion' => 'Transferencia desde tu cuenta bancaria', 'activo' => 1, 'created_at' => now()],
        ]);
    }
}
