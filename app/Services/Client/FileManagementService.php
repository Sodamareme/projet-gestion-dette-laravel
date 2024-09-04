<?php

namespace App\Services\Client;

use App\Services\Client\FileManagementInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileManagementService implements FileManagementInterface
{
    public function store(UploadedFile $file, string $path): ?string
    {
        return $file->store($path, 'public');
    }
}
