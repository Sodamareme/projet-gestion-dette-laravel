<?php
namespace App\Services\Client;
use Illuminate\Http\UploadedFile;
interface PhotoServiceInterface
{
    public function convertAndStorePhoto(UploadedFile $photo): ?string;
}
