<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->timestamp('ending_soon_notified_at')->nullable()->after('ends_at');
            $table->timestamp('finished_notified_at')->nullable()->after('ending_soon_notified_at');

            $table->index(['status', 'ends_at', 'ending_soon_notified_at'], 'campaigns_ending_soon_idx');
            $table->index(['status', 'ends_at', 'finished_notified_at'], 'campaigns_finished_notify_idx');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex('campaigns_ending_soon_idx');
            $table->dropIndex('campaigns_finished_notify_idx');
            $table->dropColumn(['ending_soon_notified_at', 'finished_notified_at']);
        });
    }
};
