<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('sensores', function (Blueprint $table) {
            $table->id(); // Este es el ID que busca la tabla de alertas
            $table->string('nombre', 100);
            $table->string('tipo', 50); // Ej. Temperatura, Humedad, pH
            $table->string('unidad', 20)->nullable();
            $table->string('pin', 10);  // Para saber a qué pin del ESP32/Arduino va conectado
            $table->foreignId('modulo_id')->nullable()->constrained('modulos')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('sensores');
    }
};
