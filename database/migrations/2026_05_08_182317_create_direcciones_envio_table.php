<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direcciones_envio', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('calle', 150);
            $table->string('numero', 20);
            $table->string('colonia', 100);
            $table->string('ciudad', 100);
            $table->string('estado', 100);
            $table->string('codigo_postal', 10);
            $table->text('referencias')->nullable();
            $table->boolean('principal')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direcciones_envio');
    }
};
