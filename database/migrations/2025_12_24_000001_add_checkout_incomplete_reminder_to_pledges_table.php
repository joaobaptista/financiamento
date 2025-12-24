<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->timestamp('checkout_incomplete_reminded_at')->nullable()->after('provider_payload');

            $table->index(['status', 'payment_method', 'checkout_incomplete_reminded_at'], 'pledges_checkout_incomplete_reminder_idx');
        });
    }

    public function down(): void
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->dropIndex('pledges_checkout_incomplete_reminder_idx');
            $table->dropColumn('checkout_incomplete_reminded_at');
        });
    }
};
