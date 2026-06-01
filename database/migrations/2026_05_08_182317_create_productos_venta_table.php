<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('productos_venta', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cultivo_id')->constrained()->onDelete('cascade');
            $table->foreignId('cosecha_id')->nullable()->constrained()->onDelete('set null');
            $table->decimal('cantidad', 10, 2);
            $table->string('unidad', 10)->default('kg');
            $table->decimal('precio_unitario', 10, 2);
            $table->integer('stock')->default(0);
            $table->enum('estado', ['disponible', 'agotado', 'eliminado'])->default('disponible');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('productos_venta');
    }
};
