<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->foreignId('barbearia_id')->nullable()->after('cliente_id')->constrained('barbearias')->nullOnDelete();
            $table->boolean('usar_plano')->default(false)->after('observacoes');
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('barbearia_id');
            $table->dropColumn('usar_plano');
        });
    }
};
