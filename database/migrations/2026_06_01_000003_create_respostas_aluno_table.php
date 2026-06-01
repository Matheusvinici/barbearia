<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('respostas_aluno', function (Blueprint $table) {
            $table->id();
            $table->foreignId('historia_id')->constrained()->onDelete('cascade');
            $table->tinyInteger('etapa');
            $table->string('pergunta');
            $table->text('resposta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('respostas_aluno');
    }
};
