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
        Schema::table('seo_audits', function (Blueprint $table) {
            $table->integer('follow_up_step')->default(0)->after('response_data');
            $table->timestamp('last_follow_up_at')->nullable()->after('follow_up_step');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_audits', function (Blueprint $table) {
            $table->dropColumn(['follow_up_step', 'last_follow_up_at']);
        });
    }
};
