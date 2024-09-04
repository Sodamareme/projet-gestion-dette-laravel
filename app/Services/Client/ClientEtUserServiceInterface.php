<?php
// Chemin : app/Services/Client/ClientUserServiceInterface.php
namespace App\Services\Client;

interface ClientEtUserServiceInterface
{
    public function store(array $data);
    public function createClient(array $data);
}
