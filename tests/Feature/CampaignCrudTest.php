<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CampaignCrudTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_campaign_with_cover_upload_and_it_is_publicly_served(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        Storage::fake('public');

        $user = User::factory()->create();

        $endsAt = now()->addDays(10)->toDateString();

        $response = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/campaigns', [
                'title' => 'Minha campanha',
                'description' => 'Descrição',
                'niche' => 'arte',
                'goal_amount' => '123.45',
                'ends_at' => $endsAt,
                'cover_image' => UploadedFile::fake()->image('cover.png', 1200, 630),
            ]);

        $response->assertCreated();

        $id = (int) $response->json('data.id');
        $this->assertGreaterThan(0, $id);

        $coverPath = (string) $response->json('data.cover_image_path');
        $this->assertNotSame('', $coverPath);
        $this->assertStringStartsWith('/storage/', $coverPath);

        $relative = preg_replace('#^/storage/#', '', $coverPath);
        $this->assertNotNull($relative);
        Storage::disk('public')->assertExists($relative);

        $webp = $response->json('data.cover_image_webp_path');
        if (is_string($webp) && $webp !== '') {
            $this->assertStringStartsWith('/storage/', $webp);
            $relativeWebp = preg_replace('#^/storage/#', '', $webp);
            $this->assertNotNull($relativeWebp);
            Storage::disk('public')->assertExists($relativeWebp);
        }
    }

    public function test_user_can_delete_own_draft_campaign_and_files_are_removed(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        Storage::fake('public');

        $user = User::factory()->create();
        $endsAt = now()->addDays(10)->toDateString();

        $create = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/campaigns', [
                'title' => 'Campanha pra excluir',
                'description' => 'Descrição',
                'niche' => 'arte',
                'goal_amount' => '50.00',
                'ends_at' => $endsAt,
                'cover_image' => UploadedFile::fake()->image('cover.jpg', 800, 600),
            ]);

        $create->assertCreated();

        $id = (int) $create->json('data.id');
        $coverPath = (string) $create->json('data.cover_image_path');
        $relative = preg_replace('#^/storage/#', '', $coverPath);
        $this->assertNotNull($relative);
        Storage::disk('public')->assertExists($relative);

        $webp = $create->json('data.cover_image_webp_path');
        $relativeWebp = null;
        if (is_string($webp) && $webp !== '') {
            $relativeWebp = preg_replace('#^/storage/#', '', $webp);
            $this->assertNotNull($relativeWebp);
            Storage::disk('public')->assertExists($relativeWebp);
        }

        $delete = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->delete("/api/me/campaigns/{$id}");

        $delete->assertOk();
        $this->assertTrue((bool) $delete->json('ok'));

        Storage::disk('public')->assertMissing($relative);
        if (is_string($relativeWebp) && $relativeWebp !== '') {
            Storage::disk('public')->assertMissing($relativeWebp);
        }
    }

    public function test_user_can_delete_own_active_campaign(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $endsAt = now()->addDays(10)->toDateString();

        $create = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/campaigns', [
                'title' => 'Campanha ativa',
                'description' => 'Descrição',
                'niche' => 'arte',
                'goal_amount' => '10.00',
                'ends_at' => $endsAt,
            ]);

        $create->assertCreated();
        $id = (int) $create->json('data.id');

        $publish = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post("/api/me/campaigns/{$id}/publish", []);

        $publish->assertOk();

        $delete = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->delete("/api/me/campaigns/{$id}");

        $delete->assertOk();
        $this->assertTrue((bool) $delete->json('ok'));
    }

    public function test_user_can_update_own_active_campaign(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create();
        $endsAt = now()->addDays(10)->toDateString();

        $create = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/campaigns', [
                'title' => 'Campanha ativa',
                'description' => 'Descrição',
                'niche' => 'arte',
                'goal_amount' => '10.00',
                'ends_at' => $endsAt,
            ]);

        $create->assertCreated();
        $id = (int) $create->json('data.id');

        $publish = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post("/api/me/campaigns/{$id}/publish", []);

        $publish->assertOk();

        $update = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->put("/api/me/campaigns/{$id}", [
                'title' => 'Campanha ativa editada',
                'description' => 'Nova descrição',
                'niche' => 'arte',
                'goal_amount' => '12.00',
                'ends_at' => now()->addDays(12)->toDateString(),
                'rewards' => [],
            ]);

        $update->assertOk();
        $this->assertSame('Campanha ativa editada', $update->json('data.title'));
    }
}
