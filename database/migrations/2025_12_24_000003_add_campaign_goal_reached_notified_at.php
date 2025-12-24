<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->timestamp('goal_reached_notified_at')->nullable()->after('pledged_amount');
            $table->index(['status', 'goal_reached_notified_at'], 'campaigns_goal_reached_notify_idx');
        });
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropIndex('campaigns_goal_reached_notify_idx');
            $table->dropColumn('goal_reached_notified_at');
        });
    }
};
