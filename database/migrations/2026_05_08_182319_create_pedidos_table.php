<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_cliente')->constrained('users')->onDelete('cascade');
            $table->foreignId('user_id_vendedor')->constrained('users')->onDelete('cascade');
            $table->foreignId('id_direccion_envio')->constrained('direcciones_envio');
            $table->foreignId('id_metodo_pago')->constrained('metodos_pago');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('impuesto', 10, 2)->default(0);
            $table->decimal('total_final', 10, 2);
            $table->enum('estado', ['pendiente', 'confirmado', 'enviado', 'entregado', 'cancelado'])->default('pendiente');
            $table->dateTime('fecha_pedido');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
