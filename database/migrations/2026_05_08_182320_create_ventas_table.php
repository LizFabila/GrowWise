<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_vendedor')->constrained('users');
            $table->foreignId('user_id_cliente')->constrained('users');
            $table->foreignId('pedido_id')->constrained();
            $table->decimal('total', 10, 2);
            $table->date('fecha_venta');
            $table->enum('estado', ['completada', 'cancelada'])->default('completada');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
