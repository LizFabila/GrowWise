<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->string('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        DB::table('metodos_pago')->insert([
            ['nombre' => 'Efectivo', 'descripcion' => 'Pago en efectivo al momento de la entrega', 'activo' => 1, 'created_at' => now()],
            ['nombre' => 'Tarjeta de crédito/débito', 'descripcion' => 'Pago con tarjeta VISA o Mastercard', 'activo' => 1, 'created_at' => now()],
            ['nombre' => 'Transferencia bancaria', 'descripcion' => 'Transferencia desde tu cuenta bancaria', 'activo' => 1, 'created_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('metodos_pago');
    }
};
