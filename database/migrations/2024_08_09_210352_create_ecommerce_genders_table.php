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
        Schema::create('ecommerce_genders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('ecommerce_genderables', function (Blueprint $table) {
            $table->foreignUuid('gender_id')->constrained('ecommerce_genders')->cascadeOnDelete();

            $table->uuidMorphs('genderable');

            $table->unique(['gender_id', 'genderable_id', 'genderable_type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ecommerce_genders');
        Schema::dropIfExists('ecommerce_genderables');
    }
};
