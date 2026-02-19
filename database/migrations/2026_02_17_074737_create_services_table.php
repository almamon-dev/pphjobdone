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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('subtitle')->nullable();
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->json('features')->nullable(); // List of features/highlights
            $table->json('pricing_plans')->nullable(); // JSON object for pricing tier
            $table->json('faqs')->nullable(); // JSON object for FAQs
            $table->json('process_steps')->nullable();
            $table->json('section_one')->nullable(); // Title, Subtitle, Image
            $table->json('section_two')->nullable(); // Title, Subtitle, Image
            $table->json('benefits')->nullable(); // Title, Description, Image
            $table->string('video_file')->nullable();
            $table->string('video_url')->nullable();
            $table->json('timeline')->nullable();
            $table->json('expect_results')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
