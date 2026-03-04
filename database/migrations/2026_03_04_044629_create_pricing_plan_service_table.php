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
        // Drop the single service_id from pricing_plans if it exists
        if (Schema::hasColumn('pricing_plans', 'service_id')) {
            Schema::table('pricing_plans', function (Blueprint $table) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            });
        }

        // Create the pivot table for many-to-many relationship
        Schema::create('pricing_plan_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pricing_plan_id')->constrained()->onDelete('cascade');
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_plan_service');

        Schema::table('pricing_plans', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('id')->constrained()->onDelete('cascade');
        });
    }
};
