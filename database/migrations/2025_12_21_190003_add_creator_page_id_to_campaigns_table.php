<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->foreignId('creator_page_id')
                ->nullable()
                ->after('user_id')
                ->constrained('creator_pages')
                ->nullOnDelete();
        });

        // Backfill: create one default page per campaign owner (if missing) and link campaigns.
        $userIds = DB::table('campaigns')->select('user_id')->distinct()->pluck('user_id');

        foreach ($userIds as $userId) {
            $user = DB::table('users')->where('id', $userId)->first();
            if (!$user) {
                continue;
            }

            $existingPageId = DB::table('creator_pages')
                ->where('owner_user_id', $userId)
                ->orderBy('id')
                ->value('id');

            $pageId = $existingPageId;

            if (!$pageId) {
                $baseSlug = Str::slug((string) ($user->name ?? 'criador'));
                $slug = $baseSlug;

                // Ensure unique slug.
                if (DB::table('creator_pages')->where('slug', $slug)->exists()) {
                    $slug = $baseSlug . '-' . $userId;
                }

                $pageId = DB::table('creator_pages')->insertGetId([
                    'owner_user_id' => $userId,
                    'name' => (string) ($user->name ?? 'Criador'),
                    'slug' => $slug,
                    'primary_category' => null,
                    'subcategory' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('campaigns')
                ->where('user_id', $userId)
                ->whereNull('creator_page_id')
                ->update(['creator_page_id' => $pageId]);
        }
    }

    public function down(): void
    {
        Schema::table('campaigns', function (Blueprint $table) {
            $table->dropConstrainedForeignId('creator_page_id');
        });
    }
};
