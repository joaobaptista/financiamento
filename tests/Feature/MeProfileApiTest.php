<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MeProfileApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_update_profile_basic_fields(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $res = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/profile', [
                'name' => 'New Name',
                'email' => 'new@example.com',
                'phone' => '51999999999',
                'postal_code' => '90050102',
            ]);

        $res->assertOk();

        $user->refresh();
        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.com', $user->email);
        $this->assertSame('51999999999', $user->phone);
        $this->assertSame('90050102', $user->postal_code);
    }

    public function test_user_can_upload_profile_photo(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);
        Storage::fake('public');

        $user = User::factory()->create();

        $file = UploadedFile::fake()->image('avatar.jpg', 200, 200);

        $res = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/profile', [
                'name' => $user->name,
                'email' => $user->email,
                'profile_photo' => $file,
            ]);

        $res->assertOk();

        $user->refresh();
        $this->assertNotNull($user->profile_photo_path);
        Storage::disk('public')->assertExists($user->profile_photo_path);
    }

    public function test_user_can_update_password_via_api(): void
    {
        $this->withoutMiddleware(VerifyCsrfToken::class);

        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $res = $this
            ->actingAs($user)
            ->withHeader('Accept', 'application/json')
            ->post('/api/me/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $res->assertOk();
        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }
}
