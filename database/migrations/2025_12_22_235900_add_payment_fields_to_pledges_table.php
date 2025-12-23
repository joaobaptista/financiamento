<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->string('payment_method')->default('card')->after('status');
            $table->json('provider_payload')->nullable()->after('provider_payment_id');

            $table->index(['payment_method']);
        });
    }

    public function down(): void
    {
        Schema::table('pledges', function (Blueprint $table) {
            $table->dropIndex(['payment_method']);
            $table->dropColumn(['payment_method', 'provider_payload']);
        });
    }
};
