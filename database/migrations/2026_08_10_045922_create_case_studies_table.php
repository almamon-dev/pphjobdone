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
        Schema::create('case_studies', function (Blueprint $table) {
            $table->id();
            $table->string('category');
            $table->string('title');
            $table->string('stats');
            $table->string('image');
            $table->string('video_bg_image')->nullable();
            $table->string('client_type')->default('Online Retailer');
            $table->string('location')->default('United States');
            $table->string('service_provided')->default('SEO Monthly + Content Writing');
            $table->string('duration')->default('6 Months');
            $table->text('challenge_description')->nullable();
            $table->json('challenge_items')->nullable();
            $table->text('approach_description')->nullable();
            $table->json('approach_items')->nullable();
            $table->text('results_description')->nullable();
            $table->json('results_items')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_studies');
    }
};

