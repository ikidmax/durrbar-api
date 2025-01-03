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
        Schema::create('ecommerce_colors', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('label');
            $table->string('code');
            $table->timestamps();
        });

        Schema::create('ecommerce_colorables', function (Blueprint $table) {
            $table->foreignUuid('color_id')->constrained('ecommerce_colors')->cascadeOnDelete();

            $table->uuidMorphs('colorable');

            $table->unique(['color_id', 'colorable_id', 'colorable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_colors');
        Schema::dropIfExists('ecommerce_colorables');
    }
};
