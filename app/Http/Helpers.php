<?php

use ImageKit\ImageKit;
use Cloudinary\Cloudinary;


if (!function_exists('generate_user_verification_code')) {
    function generate_user_verification_code(): int
    {
        return mt_rand(1000, 9999);
    }
}

if (!function_exists('image_kit_client')) {
    function image_kit_client()
    {
        return new ImageKit(
            config('services.imagekit.public_key'),
            config('services.imagekit.private_key'),
            config('services.imagekit.url_endpoint')
        );
    }
}

if (!function_exists('upload_image')) {
    function upload_image($file, $folder = 'uploads')
    {
        try {
            $imageKit = image_kit_client();
            $fileName = time() . '_' . $file->getClientOriginalName();

            $base64 = base64_encode(file_get_contents($file->getRealPath()));

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

if (!function_exists('get_relative_path')) {
    function get_relative_path($url): string|false|null
    {
        return parse_url($url, PHP_URL_PATH);
    }
}