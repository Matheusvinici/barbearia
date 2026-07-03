<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barbearia_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('barbearia_id')->constrained()->onDelete('cascade');
            $table->boolean('ativo')->default(true);
            $table->unique(['user_id', 'barbearia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barbearia_user');
    }
};
