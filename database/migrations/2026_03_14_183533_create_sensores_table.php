<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sensores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('modulo_id')->constrained()->onDelete('cascade');
            $table->string('nombre', 50);
            $table->enum('tipo', ['Temperatura', 'Humedad', 'Luz', 'pH', 'Nutrientes']);
            $table->string('unidad', 10);
            $table->string('ubicacion', 50)->nullable();
            $table->boolean('activo')->default(true);
            $table->decimal('ultima_lectura', 8, 2)->nullable();
            $table->timestamp('ultima_lectura_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sensores');
    }
};
