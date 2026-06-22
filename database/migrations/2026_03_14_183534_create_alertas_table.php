<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alertas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('modulo_id')->nullable()->constrained('modulos')->onDelete('set null');
            $table->foreignId('sensor_id')->nullable()->constrained('sensores')->onDelete('set null');
            $table->foreignId('siembra_id')->nullable()->constrained('siembras')->onDelete('set null');
            $table->string('tipo', 50);
            $table->string('titulo', 100);
            $table->text('mensaje');
            $table->string('prioridad', 20)->default('Media');
            $table->enum('estado', ['Pendiente', 'Resuelta', 'Ignorada'])->default('Pendiente');
            $table->timestamp('fecha_resolucion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alertas');
    }
};
