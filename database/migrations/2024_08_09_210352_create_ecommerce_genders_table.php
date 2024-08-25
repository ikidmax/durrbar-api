<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
            $table->uuid('id')->primary()->default(DB::raw('(UUID())'));
            $table->uuid('gender_id');
            $table->uuidMorphs('genderable');
            $table->timestamps();

            $table->foreign('gender_id')->references('id')->on('ecommerce_genders')->onDelete('cascade');
            $table->index(['genderable_id', 'genderable_type']);
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
