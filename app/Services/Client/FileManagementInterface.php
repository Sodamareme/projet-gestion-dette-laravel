<?php

namespace App\Services\Client;

use Illuminate\Http\UploadedFile;

interface FileManagementInterface
{
    public function store(UploadedFile $file, string $path): ?string;
}
