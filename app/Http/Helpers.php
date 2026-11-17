<?php

use ImageKit\ImageKit;

if (!function_exists('generate_user_verification_code')) {
    function generate_user_verification_code(): int
    {
        return mt_rand(1000, 9999);
    }
}

if (!function_exists('image_kit_client')) {
    function image_kit_client(): ImageKit
    {
        return new ImageKit(
            config('services.imagekit.public_key'),
            config('services.imagekit.private_key'),
            config('services.imagekit.url_endpoint')
        );
    }
}

if (!function_exists('upload_image')) {
    /**
     * @return array{provider: string|null, url: string|null, error?: string}
     */
    function upload_image(mixed $file, mixed $folder = 'uploads'): array
    {
        try {
            $imageKit = image_kit_client();
            $fileName = time() . '_' . $file->getClientOriginalName();

            $contents = file_get_contents($file->getRealPath());

            if ($contents === false) {
               throw new RuntimeException('Failed to read uploaded file contents.');
            }

            $base64 = base64_encode($contents);
            $uploadFile = $imageKit->uploadFile([
                'file' => "data:image/*;base64,{$base64}",
                'fileName' => $fileName,
                'folder' => $folder,
                'useUniqueFileName' => true,
                'isPublished' => true,
                'isPrivateFile' => false,
            ]);

            if (!empty($uploadFile->result->url)) {
                return $uploadFile->result->url;
            }
        } catch (Exception $e) {
            Log::error('ImageKit Upload Failed: ' . $e->getMessage());
        }
        return [
            'provider' => null,
            'url' => null,
            'error' => 'Image upload failed on all providers.',
        ];
    }
}

