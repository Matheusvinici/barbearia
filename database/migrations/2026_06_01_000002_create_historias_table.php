<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aluno_id')->constrained()->onDelete('cascade');
            $table->enum('status', ['rascunho', 'concluido'])->default('rascunho');
            $table->text('prompt_gerado')->nullable();
            $table->json('resposta_gemini')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('slug')->unique();
            $table->string('qr_code_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historias');
    }
};
