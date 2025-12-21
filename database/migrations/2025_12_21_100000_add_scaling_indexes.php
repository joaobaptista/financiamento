<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            // For creator dashboards and user-owned listing
            $table->index('user_id');

            // For public listing / active lookups
            $table->index(['status', 'created_at']);
        });

        Schema::table('pledges', function (Blueprint $table) {
            // For dashboard listing: paid pledges ordered by paid_at
            $table->index(['campaign_id', 'status', 'paid_at']);

            // Idempotency on payment confirmation
            $table->unique('provider_payment_id');
        });

        Schema::table('rewards', function (Blueprint $table) {
            // Helps querying rewards by campaign and sorting/pricing
            $table->index(['campaign_id', 'min_amount']);
        });
    }

    public function down(): void
    {
        Schema::table('rewards', function (Blueprint $table) {
            $table->dropIndex(['campaign_id', 'min_amount']);
        });

        Schema::table('pledges', function (Blueprint $table) {
            $table->dropIndex(['campaign_id', 'status', 'paid_at']);
            $table->dropUnique(['provider_payment_id']);
        });

        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex(['user_id']);
            $table->dropIndex(['status', 'created_at']);
        });
    }
};
