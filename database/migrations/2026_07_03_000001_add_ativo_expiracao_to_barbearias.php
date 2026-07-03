<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barbearias', function (Blueprint $table) {
            $table->boolean('ativo')->default(true)->after('dias_funcionamento');
            $table->date('data_expiracao')->nullable()->after('ativo');
        });
    }

    public function down(): void
    {
        Schema::table('barbearias', function (Blueprint $table) {
            $table->dropColumn(['ativo', 'data_expiracao']);
        });
    }
};
