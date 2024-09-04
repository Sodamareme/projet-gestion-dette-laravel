<?php

namespace App\Services\Client;
use App\Services\Client\UploadService;
use App\Repositories\ClientRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Facades\UploadFacade;
use App\Facades\ClientServiceFacade;
use App\Models\user;
use App\Models\Client;
use App\Services\Client\ClientServiceInterface;
use Illuminate\Support\Facades\Hash; 
use Illuminate\Http\UploadedFile;

class ClientServiceImpl implements ClientServiceInterface
{
    protected $clientRepository;

    // public function __construct(ClientRepositoryInterface $clientRepository)
    // {
    //     $this->clientRepository = $clientRepository;
    // }
    public function register(array $data): User
    {
         // Handle photo upload and conversion
         $photoPath = $data['photo'] ?? null;

        // Créer l'utilisateur
        $user = User::create([
            'nom' => $data['nom'],
            'prenom' => $data['prenom'],
            'telephone' => $data['telephone'],
            'photo' => $photoPath,
            'login' => $data['login'],
            'password' => Hash::make($data['password']),
            'role_id' => $data['role_id'], // Assigner le rôle par ID
        ]);
        // Assigner le rôle à l'utilisateur
        $user->assignRole($data['role_id']);

        return $user;
    }

    public function getAllClients(array $filters = []): Collection 
    {
        // Collecter les filtres et passer à la méthode du repository
        $filters = [
            'compte' => $filters['compte']?? null,
            'active' => $filters['active']?? null,
        ];
        return ClientServiceFacade::all($filters);
    }

    public function createClient(array $data): Client
    {
        // Handle photo upload and conversion
        $photoPath = $data['photo'] ?? null;
    
        // Create the client
        $client = Client::create([
            'surnom' => $data['surnom'],
            'telephone' => $data['telephone'],
            'adresse' => $data['adresse'] ?? null,
            'photo' => $photoPath,
        ]);
    
        return $client;
    }
    

    public function getClientById($id): ?Client
    {
        $client = ClientServiceFacade::find($id);
        if ($client && $client->photo) {
            // Utilisation de getImageAsBase64 pour récupérer l'image encodée en base64
            $client->photo_base64 = UploadFacade::getImageAsBase64($client->photo);
        }
        return $client;
    }

    public function getClientByTelephone(string $telephone): ?Client
    {
        return ClientServiceFacade::ByTelephone($telephone);
    }
}
