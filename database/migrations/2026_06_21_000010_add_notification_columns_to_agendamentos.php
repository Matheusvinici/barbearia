<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->timestamp('barber_notified_at')->nullable()->after('origem');
            $table->timestamp('lembrete_1h_at')->nullable()->after('barber_notified_at');
            $table->timestamp('lembrete_30min_at')->nullable()->after('lembrete_1h_at');
            $table->timestamp('lembrete_15min_at')->nullable()->after('lembrete_30min_at');
        });
    }

    public function down(): void
    {
        Schema::table('agendamentos', function (Blueprint $table) {
            $table->dropColumn(['barber_notified_at', 'lembrete_1h_at', 'lembrete_30min_at', 'lembrete_15min_at']);
        });
    }
};
