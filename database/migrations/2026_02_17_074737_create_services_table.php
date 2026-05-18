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
            $table->json('faqs')->nullable(); // JSON object for FAQs
            $table->json('process_steps')->nullable();
            $table->json('section_one')->nullable(); // Title, Subtitle, Image
            $table->json('section_two')->nullable(); // Title, Subtitle, Image
            $table->json('service_features')->nullable(); // Title, Description, Image
            $table->json('secondary_features')->nullable(); // Additional separate feature grid
            $table->string('video_file')->nullable();
            $table->string('video_url')->nullable();
            $table->json('timeline')->nullable();
            $table->json('expect_results')->nullable();
            $table->string('thumbnail')->nullable();
            $table->boolean('status')->default(true);
            $table->boolean('is_campaign')->default(false);
            $table->boolean('has_faq')->default(true);
            $table->boolean('has_secondary_features')->default(false);
            $table->boolean('has_benifite')->default(true);
            $table->boolean('has_why_chose_us')->default(true);
            $table->json('brands')->nullable();
            $table->boolean('has_brands')->default(true);  
            $table->boolean('has_expect_result')->default(true);  
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
