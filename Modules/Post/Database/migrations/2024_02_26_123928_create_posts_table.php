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
        Schema::create('posts', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string('title');
            $table->string('publish')->default('draft');
            $table->longText('content');
            $table->string('cover_url');
            $table->string('author_id');
            $table->text('description');
            $table->integer('total_views');
            $table->integer('total_shares');
            $table->integer('total_favorites');

            $table->string('meta_title');
            $table->string('meta_keywords');
            $table->string('meta_description');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
