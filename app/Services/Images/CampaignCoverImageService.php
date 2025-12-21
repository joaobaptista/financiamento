<?php

namespace App\Services\Images;

use App\Domain\Campaign\Campaign;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CampaignCoverImageService
{
    public function storeForCampaign(Campaign $campaign, UploadedFile $file): void
    {
        $disk = Storage::disk('public');

        $this->deleteOldFiles($campaign);

        $baseDir = 'campaign-covers/' . $campaign->id;
        $baseName = Str::uuid()->toString();

        $jpgRelativePath = $baseDir . '/' . $baseName . '.jpg';
        $webpRelativePath = $baseDir . '/' . $baseName . '.webp';

        [$jpgBytes, $webpBytes] = $this->convertToJpgAndWebp($file);

        if ($jpgBytes === null) {
            // Fallback: store the original file (keeps the feature working even without GD/Imagick).
            $ext = strtolower($file->getClientOriginalExtension() ?: 'bin');
            $originalRelativePath = $baseDir . '/' . $baseName . '.' . $ext;

            $disk->putFileAs($baseDir, $file, $baseName . '.' . $ext);

            $campaign->forceFill([
                'cover_image_path' => $originalRelativePath,
                'cover_image_webp_path' => null,
            ])->save();

            return;
        }

        $disk->put($jpgRelativePath, $jpgBytes);

        $finalWebpPath = null;
        if ($webpBytes !== null) {
            $disk->put($webpRelativePath, $webpBytes);
            $finalWebpPath = $webpRelativePath;
        }

        $campaign->forceFill([
            'cover_image_path' => $jpgRelativePath,
            'cover_image_webp_path' => $finalWebpPath,
        ])->save();
    }

    private function deleteOldFiles(Campaign $campaign): void
    {
        $disk = Storage::disk('public');

        foreach (['cover_image_path', 'cover_image_webp_path'] as $key) {
            $raw = $campaign->getRawOriginal($key);
            if (!$raw) {
                continue;
            }

            $raw = trim((string) $raw);
            if ($raw === '' || preg_match('#^[a-z][a-z0-9+\-.]*://#i', $raw) === 1 || str_starts_with($raw, 'data:')) {
                continue;
            }

            $relative = $this->toPublicDiskRelativePath($raw);
            if ($relative === null) {
                continue;
            }

            // Only delete files we own (avoid deleting arbitrary public-disk files).
            if (!str_starts_with($relative, 'campaign-covers/')) {
                continue;
            }

            $disk->delete($relative);
        }
    }

    private function toPublicDiskRelativePath(string $value): ?string
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }

        if (str_starts_with($value, '/storage/')) {
            return ltrim(substr($value, strlen('/storage/')), '/');
        }

        if (str_starts_with($value, 'storage/')) {
            return ltrim(substr($value, strlen('storage/')), '/');
        }

        if (str_starts_with($value, '/')) {
            // Unknown absolute path under public; can't map safely.
            return null;
        }

        return $value;
    }

    /**
     * @return array{0: string|null, 1: string|null}
     */
    private function convertToJpgAndWebp(UploadedFile $file): array
    {
        if (!function_exists('imagecreatefromstring') || !function_exists('imagesx') || !function_exists('imagesy')) {
            return [null, null];
        }

        $bytes = @file_get_contents($file->getRealPath());
        if ($bytes === false) {
            return [null, null];
        }

        $src = @imagecreatefromstring($bytes);
        if (!$src) {
            return [null, null];
        }

        $srcW = imagesx($src);
        $srcH = imagesy($src);

        $maxW = 1600;
        $dst = $src;

        if ($srcW > $maxW) {
            $newW = $maxW;
            $newH = (int) round(($srcH * $newW) / $srcW);
            $scaled = imagescale($src, $newW, $newH, IMG_BICUBIC);
            if ($scaled) {
                $dst = $scaled;
            }
        }

        // JPG: flatten transparency to white.
        $dstW = imagesx($dst);
        $dstH = imagesy($dst);
        $jpgCanvas = imagecreatetruecolor($dstW, $dstH);
        $white = imagecolorallocate($jpgCanvas, 255, 255, 255);
        imagefill($jpgCanvas, 0, 0, $white);
        imagecopy($jpgCanvas, $dst, 0, 0, 0, 0, $dstW, $dstH);

        $jpgBytes = $this->encodeJpeg($jpgCanvas, 82);
        imagedestroy($jpgCanvas);

        $webpBytes = null;
        if (function_exists('imagewebp')) {
            $webpBytes = $this->encodeWebp($dst, 80);
        }

        if ($dst !== $src) {
            imagedestroy($dst);
        }
        imagedestroy($src);

        return [$jpgBytes, $webpBytes];
    }

    private function encodeJpeg($image, int $quality): ?string
    {
        ob_start();
        $ok = imagejpeg($image, null, $quality);
        $data = ob_get_clean();

        if (!$ok || $data === false) {
            return null;
        }

        return $data;
    }

    private function encodeWebp($image, int $quality): ?string
    {
        ob_start();
        $ok = imagewebp($image, null, $quality);
        $data = ob_get_clean();

        if (!$ok || $data === false) {
            return null;
        }

        return $data;
    }
}
