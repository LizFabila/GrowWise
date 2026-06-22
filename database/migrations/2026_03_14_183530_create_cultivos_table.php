<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cultivos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->enum('tipo', ['Hoja', 'Fruto', 'Aromática', 'Raíz', 'Otro']);
            $table->text('descripcion')->nullable();
            $table->decimal('temperatura_optima_min', 4, 2)->nullable();
            $table->decimal('temperatura_optima_max', 4, 2)->nullable();
            $table->unsignedTinyInteger('humedad_optima_min')->nullable();
            $table->unsignedTinyInteger('humedad_optima_max')->nullable();
            $table->unsignedInteger('luz_optima_min')->nullable();
            $table->unsignedInteger('luz_optima_max')->nullable();
            $table->decimal('ph_optimo_min', 3, 1)->nullable();
            $table->decimal('ph_optimo_max', 3, 1)->nullable();
            $table->unsignedSmallInteger('dias_cosecha')->nullable();
            $table->string('imagen')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cultivos');
    }
};
