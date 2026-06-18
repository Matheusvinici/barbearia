<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('despesas', function (Blueprint $table) {
            $table->id();
            $table->string('descricao');
            $table->decimal('valor', 10, 2);
            $table->date('data_vencimento');
            $table->date('data_pagamento')->nullable();
            $table->string('categoria', 50);
            $table->string('forma_pagamento', 50)->nullable();
            $table->boolean('pago')->default(false);
            $table->text('observacoes')->nullable();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('despesas');
    }
};
