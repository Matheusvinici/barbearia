<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('bloqueio_agendas', function (Blueprint $table) {
            $table->foreignId('barbearia_id')->nullable()->after('barbeiro_id')->constrained('barbearias')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('bloqueio_agendas', function (Blueprint $table) {
            $table->dropConstrainedForeignId('barbearia_id');
        });
    }
};
