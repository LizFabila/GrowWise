<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('siembras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('cultivo_id')->constrained()->onDelete('cascade');
            $table->foreignId('modulo_id')->constrained()->onDelete('cascade');
            $table->unsignedTinyInteger('charola');
            $table->date('fecha_siembra');
            $table->unsignedInteger('cantidad_semillas')->nullable();
            $table->date('fecha_estimada_cosecha')->nullable();
            $table->date('fecha_cosecha_real')->nullable();
            $table->enum('estado', ['Activa', 'Completada', 'Problema', 'Cancelada'])->default('Activa');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('siembras');
    }
};
