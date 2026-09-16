<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cliente_plano', function (Blueprint $table) {
            if (!Schema::hasColumn('cliente_plano', 'barbearia_id')) {
                $table->foreignId('barbearia_id')->nullable()->after('cliente_id')->constrained('barbearias')->nullOnDelete();
            }
            if (!Schema::hasColumn('cliente_plano', 'valor_pago')) {
                $table->decimal('valor_pago', 10, 2)->nullable()->after('data_fim');
            }
            if (!Schema::hasColumn('cliente_plano', 'forma_pagamento')) {
                $table->string('forma_pagamento', 50)->nullable()->after('valor_pago');
            }
            if (!Schema::hasColumn('cliente_plano', 'pago')) {
                $table->boolean('pago')->default(false)->after('forma_pagamento');
            }
            if (!Schema::hasColumn('cliente_plano', 'pago_em')) {
                $table->dateTime('pago_em')->nullable()->after('pago');
            }
            if (!Schema::hasColumn('cliente_plano', 'vencimento')) {
                // alias for data_fim, but allow separate vencimento if needed; nullable
                $table->date('vencimento')->nullable()->after('pago_em');
            }
        });

        Schema::table('agendamentos', function (Blueprint $table) {
            if (!Schema::hasColumn('agendamentos', 'cliente_plano_id')) {
                $table->foreignId('cliente_plano_id')->nullable()->after('cliente_id')->constrained('cliente_plano')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('cliente_plano', function (Blueprint $table) {
            if (Schema::hasColumn('cliente_plano', 'barbearia_id')) {
                $table->dropConstrainedForeignId('barbearia_id');
            }
            $cols = ['valor_pago','forma_pagamento','pago','pago_em','vencimento'];
            foreach ($cols as $c) {
                if (Schema::hasColumn('cliente_plano', $c)) {
                    $table->dropColumn($c);
                }
            }
        });
        Schema::table('agendamentos', function (Blueprint $table) {
            if (Schema::hasColumn('agendamentos', 'cliente_plano_id')) {
                $table->dropConstrainedForeignId('cliente_plano_id');
            }
        });
    }
};
