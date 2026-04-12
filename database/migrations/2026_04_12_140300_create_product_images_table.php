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
        Schema::create('product_images', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnUpdate()->cascadeOnDelete();
            $table->text('image_url');
            $table->string('image_url_hash', 64);
            $table->boolean('is_primary')->default(false);
            $table->unsignedInteger('sort_order')->default(1);
            $table->string('estado', 30)->default('activo');
            $table->timestamps();

            $table->index(['product_id', 'estado']);
            $table->unique(['product_id', 'image_url_hash']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_images');
    }
};
