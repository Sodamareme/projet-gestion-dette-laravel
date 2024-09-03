<?php

namespace App\Services;

use App\Repositories\ClientRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Collection;
use App\Services\UploadService;
use App\Facades\UploadFacade;
use App\Facades\ClientServiceFacade;
use App\Models\Client;

class ClientServiceImpl implements ClientServiceInterface
{
    protected $clientRepository;

    // public function __construct(ClientRepositoryInterface $clientRepository)
    // {
    //     $this->clientRepository = $clientRepository;
    // }

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
        // Traitement de la photo
        if (isset($data['photo']) && $data['photo'] instanceof \Illuminate\Http\UploadedFile) {
            // $data['photo'] = $data['photo']->store('photos', 'public');
             $data['photo'] = UploadFacade::uploadImage($data['photo']);;
        }

        $userData = isset($data['user']) ? [
            'nom' => $data['user']['nom'],
            'prenom' => $data['user']['prenom'],
            'login' => $data['user']['login'],
            'password' => bcrypt($data['user']['password']),
            'etat' => $data['user']['etat'],
            'role_id' => $data['user']['role'],
        ] : null;

        return $this->clientRepository->create($data, $userData);
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
