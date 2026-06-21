<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barbeiro_horarios', function (Blueprint $table) {
            $table->string('periodo', 20)->nullable()->after('dia_semana')->comment('manha, tarde, noite');
        });
    }

    public function down(): void
    {
        Schema::table('barbeiro_horarios', function (Blueprint $table) {
            $table->dropColumn('periodo');
        });
    }
};
