<?php

use App\Enum\AVCName;
use App\Models\Card;
use App\Models\Configuration;
use App\Models\ConversionBoard;
use App\Models\Country;
use App\Models\Currency;
use App\Models\Mailing;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAccount;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use ImageKit\ImageKit;


// if (! function_exists('get_relative_path')) {
//     function get_relative_path($url): string|false|null
//     {
//         return parse_url($url, PHP_URL_PATH);
//     }
// }

// if (! function_exists('upload_image')) {
//     function upload_image($file, $folder = 'uploads')
//     {
//         try {
//             // 1. Try ImageKit
//             $imageKit = image_kit_client();
//             $fileName = time().'_'.$file->getClientOriginalName();

//             $base64 = base64_encode(file_get_contents($file->getRealPath()));

//             $uploadFile = $imageKit->uploadFile([
//                 'file' => "data:image/*;base64,{$base64}",
//                 'fileName' => $fileName,
//                 'folder' => $folder,
//                 'useUniqueFileName' => true,
//                 'isPublished' => true,
//                 'isPrivateFile' => false,
//             ]);

//             if (! empty($uploadFile->result->url)) {
//                 return $uploadFile->result->url;
//             }
//         } catch (Exception $e) {
//             Log::error('ImageKit Upload Failed: '.$e->getMessage());
//         }

//         try {

//             $uploadedImage = Cloudinary::uploadApi()->upload($file->getRealPath(), [
//                 'folder' => $folder,
//             ]);

//             if (! empty($uploadedImage['secure_url'])) {
//                 return $uploadedImage['secure_url'];
//             }
//         } catch (Exception $e) {
//             Log::error('Cloudinary Upload Failed: '.$e->getMessage());
//         }

//         return [
//             'provider' => null,
//             'url' => null,
//             'error' => 'Image upload failed on all providers.',
//         ];
//     }
// }

// if (! function_exists('mail_send')) {
//     /**
//      * @param  array<string, mixed>  $payloadData
//      */
//     function mail_send(string $type, User $recipient, string $subject, string $mailClass, array $payloadData = []): void
//     {
//         $data = [
//             'type' => $type,
//             'email' => $recipient->email,
//             'subject' => $subject,
//             'body' => '',
//             'mailable' => $mailClass,
//             'scheduled_at' => now(),
//             'payload' => array_merge($payloadData),
//         ];

//         Mailing::saveData($data);
//     }
// }

if (! function_exists('generate_user_verification_code')) {
    function generate_user_verification_code(): int
    {
        return mt_rand(1000, 9999);
    }
}

