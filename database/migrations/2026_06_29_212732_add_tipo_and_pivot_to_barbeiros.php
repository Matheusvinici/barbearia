<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barbeiros', function (Blueprint $table) {
            $table->string('tipo', 20)->default('funcionario')->after('comissao_percentual');
            $table->string('especialidades', 500)->nullable()->after('tipo');
        });

        Schema::create('barbeiro_barbearia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barbeiro_id')->constrained()->cascadeOnDelete();
            $table->foreignId('barbearia_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['barbeiro_id', 'barbearia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barbeiro_barbearia');

        Schema::table('barbeiros', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'especialidades']);
        });
    }
};
