<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('caixas', function (Blueprint $table) {
            $table->id();
            $table->date('data')->unique();
            $table->decimal('saldo_inicial', 10, 2)->default(0);
            $table->decimal('total_entradas', 10, 2)->default(0);
            $table->decimal('total_saidas', 10, 2)->default(0);
            $table->decimal('saldo_final', 10, 2)->default(0);
            $table->boolean('fechado')->default(false);
            $table->text('observacoes')->nullable();
            $table->foreignId('user_id_abertura')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('user_id_fechamento')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('caixa_movimentacoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caixa_id')->constrained('caixas')->onDelete('cascade');
            $table->enum('tipo', ['entrada', 'saida']);
            $table->decimal('valor', 10, 2);
            $table->string('descricao');
            $table->string('origem_type')->nullable();
            $table->unsignedBigInteger('origem_id')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('caixa_movimentacoes');
        Schema::dropIfExists('caixas');
    }
};
