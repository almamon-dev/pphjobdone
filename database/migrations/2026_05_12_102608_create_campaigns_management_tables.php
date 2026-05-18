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
        // 1. Campaigns Table (Groups: Blog Writing, Social Media, Branding)
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('service_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('subtitle')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 2. Campaign Tiers Table (Slider Points: $5, $10, $25...)
        Schema::create('campaign_tiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained()->onDelete('cascade');
            $table->decimal('price', 10, 2);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 3. Campaign Features Table (Specific features for each Price Tier)
        Schema::create('campaign_features', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_tier_id')->constrained()->onDelete('cascade');
            $table->string('feature_text');
            $table->json('sub_items')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
        });

        // 4. Update Bookings Table to include campaign_tier_id and is_campaign
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                if (!Schema::hasColumn('bookings', 'campaign_tier_id')) {
                    $table->foreignId('campaign_tier_id')->nullable()->after('pricing_plan_id')->constrained()->onDelete('set null');
                }
                if (!Schema::hasColumn('bookings', 'is_campaign')) {
                    $table->boolean('is_campaign')->default(false)->after('payment_status');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('bookings')) {
            Schema::table('bookings', function (Blueprint $table) {
                $table->dropForeign(['campaign_tier_id']);
                $table->dropColumn(['campaign_tier_id', 'is_campaign']);
            });
        }
        Schema::dropIfExists('campaign_features');
        Schema::dropIfExists('campaign_tiers');
        Schema::dropIfExists('campaigns');
    }
};
