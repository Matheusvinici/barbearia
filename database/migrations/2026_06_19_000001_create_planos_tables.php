<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planos', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->text('descricao')->nullable();
            $table->decimal('valor', 10, 2)->default(0);
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });

        Schema::create('plano_servico_quotas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plano_id')->constrained()->onDelete('cascade');
            $table->foreignId('servico_id')->constrained()->onDelete('cascade');
            $table->integer('quantidade')->default(0);
            $table->timestamps();
        });

        Schema::create('cliente_plano', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained()->onDelete('cascade');
            $table->foreignId('plano_id')->constrained()->onDelete('cascade');
            $table->date('data_inicio');
            $table->date('data_fim')->nullable();
            $table->boolean('ativo')->default(true);
            $table->text('observacoes')->nullable();
            $table->timestamps();
        });

        Schema::create('cliente_plano_usos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_plano_id')->constrained('cliente_plano')->onDelete('cascade');
            $table->foreignId('agendamento_id')->constrained()->onDelete('cascade');
            $table->foreignId('servico_id')->constrained()->onDelete('cascade');
            $table->dateTime('usado_em');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cliente_plano_usos');
        Schema::dropIfExists('cliente_plano');
        Schema::dropIfExists('plano_servico_quotas');
        Schema::dropIfExists('planos');
    }
};
