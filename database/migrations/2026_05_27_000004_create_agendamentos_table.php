<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barbeiro_id')->constrained('barbeiros')->onDelete('cascade');
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('cascade');
            $table->date('data');
            $table->time('hora_inicio');
            $table->time('hora_fim');
            $table->enum('status', ['pendente', 'confirmado', 'realizado', 'cancelado', 'ausente'])->default('pendente');
            $table->decimal('total', 10, 2)->nullable();
            $table->text('observacoes')->nullable();
            $table->unsignedBigInteger('created_by')->nullable()->comment('Admin user who created it');
            $table->string('origem', 20)->default('admin')->comment('admin, bot');
            $table->timestamps();
        });

        Schema::create('agendamento_servico', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agendamento_id')->constrained('agendamentos')->onDelete('cascade');
            $table->foreignId('servico_id')->constrained('servicos')->onDelete('cascade');
            $table->decimal('preco_praticado', 10, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendamento_servico');
        Schema::dropIfExists('agendamentos');
    }
};
