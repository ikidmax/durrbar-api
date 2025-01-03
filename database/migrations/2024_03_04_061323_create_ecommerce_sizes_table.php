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
        Schema::create('ecommerce_sizes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('size');
            $table->timestamps();
        });

        Schema::create('ecommerce_sizeables', function (Blueprint $table) {
            $table->foreignUuid('size_id')->constrained('ecommerce_sizes')->cascadeOnDelete();

            $table->uuidMorphs('sizeable');

            $table->unique(['size_id', 'sizeable_id', 'sizeable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_sizes');
        Schema::dropIfExists('ecommerce_sizeables');
    }
};
