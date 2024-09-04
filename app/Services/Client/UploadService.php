<?php

namespace App\Services\Client;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UploadService
{
    /**
     * Upload an image and store it in the specified directory.
     *
     * @param UploadedFile $file
     * @param string $directory
     * @return string The path to the stored image
     */
    public function uploadImage(UploadedFile $file, string $directory = 'photos'): string
    {
        return $file->store($directory, 'public');
    }

    /**
     * Retrieve the image as a base64 encoded string.
     *
     * @param string $path
     * @return string
     */

}
