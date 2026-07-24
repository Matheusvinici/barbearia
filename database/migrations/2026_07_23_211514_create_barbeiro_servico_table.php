<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barbeiro_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barbeiro_id')->constrained()->cascadeOnDelete();
            $table->foreignId('servico_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['barbeiro_id', 'servico_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barbeiro_servico');
    }
};
