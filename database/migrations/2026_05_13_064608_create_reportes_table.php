<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('nombre');
            $table->string('tipo'); // cultivos, siembras, cosechas, monitoreo, alertas
            $table->date('periodo_inicio')->nullable();
            $table->date('periodo_fin')->nullable();
            $table->enum('formato', ['PDF', 'Excel', 'CSV'])->default('PDF');
            $table->string('archivo_url')->nullable();
            $table->integer('tamaño_kb')->unsigned()->nullable();
            $table->json('parametros')->nullable();
            $table->boolean('descargado')->default(false);
            $table->timestamps();

            // Índices
            $table->index('user_id');
            $table->index('tipo');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reportes');
    }
};
