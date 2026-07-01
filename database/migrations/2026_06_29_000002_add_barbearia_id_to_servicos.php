<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->foreignId('barbearia_id')->nullable()->constrained('barbearias')->nullOnDelete()->after('id');
        });
    }

    public function down(): void
    {
        Schema::table('servicos', function (Blueprint $table) {
            $table->dropForeign(['barbearia_id']);
            $table->dropColumn('barbearia_id');
        });
    }
};
