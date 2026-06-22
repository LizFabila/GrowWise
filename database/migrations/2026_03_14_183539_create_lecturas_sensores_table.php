<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lecturas_sensores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sensor_id')->constrained('sensores')->onDelete('cascade');
            $table->decimal('valor', 8, 2);
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lecturas_sensores');
    }
};
