<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone', 30)->nullable()->after('email');
            $table->enum('user_type', ['administrativo', 'cliente'])->default('cliente')->after('phone');
            $table->string('document_number', 50)->nullable()->after('user_type');
            $table->string('address')->nullable()->after('document_number');
            $table->string('estado', 30)->default('activo')->after('remember_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'user_type',
                'document_number',
                'address',
                'estado',
            ]);
        });
    }
};
