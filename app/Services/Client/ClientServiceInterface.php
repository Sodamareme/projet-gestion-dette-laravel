<?php

namespace App\Services\Client;
use App\Services\Client\UploadService;
use App\Models\Client;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
interface ClientServiceInterface
{
    public function register(array $data): User;
    public function getAllClients(array $filters): Collection;
    public function createClient(array $data): Client;
    public function getClientById($id): ?Client;
    public function getClientByTelephone(string $telephone): ?Client;
}