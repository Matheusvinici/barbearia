<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barbearias', function (Blueprint $table) {
            $table->string('horario_abertura', 5)->default('08:00');
            $table->string('horario_fechamento', 5)->default('18:00');
            $table->integer('intervalo_minutos')->default(30);
            $table->string('dias_funcionamento', 50)->default('1,2,3,4,5,6');
        });
    }

    public function down(): void
    {
        Schema::table('barbearias', function (Blueprint $table) {
            $table->dropColumn(['horario_abertura', 'horario_fechamento', 'intervalo_minutos', 'dias_funcionamento']);
        });
    }
};
