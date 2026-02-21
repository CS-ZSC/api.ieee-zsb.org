<?php

namespace App\Traits;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait ImageUploadTrait
{
    /**
     * Upload image and return path
     */
    public function uploadImage(UploadedFile $file, string $folder, string $filename = null): string
    {
        // Generate unique filename
        $filename = $filename ?: Str::uuid() . '.' . $file->getClientOriginalExtension();

        // Store file
        $path = $file->storeAs($folder, $filename, 'public');

        return $path;
    }

    /**
     * Delete old image if exists
     */
    public function deleteOldImage(?string $oldImagePath): void
    {
        if ($oldImagePath && Storage::disk('public')->exists($oldImagePath)) {
            Storage::disk('public')->delete($oldImagePath);
        }
    }

    /**
     * Get full URL for image path
     */
    public function getImageUrl(string $path): string
    {
        return $path ? Storage::url($path) : null;
    }

    /**
     * Validate image file
     */
    public function validateImage(UploadedFile $file, int $maxSize = 2048): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:' . $maxSize,
            'image.*' => 'mimes:jpeg,jpg,png,gif,webp|max:' . $maxSize,
        ];
    }

    /**
     * Validate multiple image files
     */
    public function validateMultipleImages(array $files, int $maxSize = 2048, int $maxFiles = 10): array
    {
        return [
            'images' => 'required|array|max:' . $maxFiles,
            'images.*' => 'required|image|mimes:jpeg,jpg,png,gif,webp|max:' . $maxSize,
        ];
    }

    /**
     * Get image validation rules for different contexts
     */
    public function getImageValidationRules(string $context = 'default'): string
    {
        $rules = [
            'default' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
            'avatar' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:1024', // Smaller for avatars
            'logo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:4096', // Larger for logos
            'cover' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:4096', // Larger for covers
            'gallery' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:2048',
        ];

        return $rules[$context] ?? $rules['default'];
    }
}
