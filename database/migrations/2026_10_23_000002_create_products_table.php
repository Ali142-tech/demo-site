<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('icon')->default('sheet');
            $table->string('tagline');
            $table->text('description');
            $table->json('features')->nullable();
            $table->json('options')->nullable();
            $table->decimal('starting_price', 10, 2)->nullable();
            $table->string('price_unit')->default('piece');
            $table->unsignedInteger('min_quantity')->default(1);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};