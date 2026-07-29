<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\Laravel\Facades\Image;

class ImageService
{
    /**
     * Compress uploaded image, convert to WebP format, and save to storage disk.
     *
     * @param UploadedFile $file
     * @param string $folder
     * @param int $maxWidth
     * @param int $quality
     * @return string
     */
    public static function compressAndStore(
        UploadedFile $file,
        string $folder = 'uploads',
        int $maxWidth = 1500,
        int $quality = 80
    ): string {
        $filename = $folder . '/' . Str::uuid() . '.webp';

        // Decode uploaded image file
        $image = Image::decode($file);

        // Scale down if image width exceeds max width
        if ($image->width() > $maxWidth) {
            $image->scale(width: $maxWidth);
        }

        // Convert to WebP format
        $encoded = $image->encode(new WebpEncoder(quality: $quality));

        // Store to public storage disk
        Storage::disk('public')->put($filename, (string) $encoded);

        return $filename;
    }
}
