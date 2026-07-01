<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barbearias', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable()->after('nome');
            $table->string('logo')->nullable()->after('cidade');
            $table->string('background_image')->nullable()->after('logo');
            $table->foreignId('owner_id')->nullable()->constrained('users')->nullOnDelete()->after('background_image');
        });
    }

    public function down(): void
    {
        Schema::table('barbearias', function (Blueprint $table) {
            $table->dropForeign(['owner_id']);
            $table->dropColumn(['slug', 'logo', 'background_image', 'owner_id']);
        });
    }
};
