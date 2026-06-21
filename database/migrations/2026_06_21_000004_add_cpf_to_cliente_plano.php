<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cliente_plano', function (Blueprint $table) {
            $table->string('cpf', 14)->nullable()->after('observacoes');
        });
    }

    public function down(): void
    {
        Schema::table('cliente_plano', function (Blueprint $table) {
            $table->dropColumn('cpf');
        });
    }
};
