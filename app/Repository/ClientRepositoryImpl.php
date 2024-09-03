<?php

namespace App\Repository;

use App\Models\Client;
use Illuminate\Database\Eloquent\Collection;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Repository\ClientRepositoryInterface;


class ClientRepositoryImpl implements ClientRepositoryInterface

{
    public function all($filters): Collection
    {
        $query = QueryBuilder::for(Client::class)
            ->allowedFilters(['surname'])
            ->allowedIncludes(['user']);
        
        if (isset($filters['compte'])) {
            if ($filters['compte'] === 'oui') {
                $query->whereNotNull('user_id');
            } elseif ($filters['compte'] === 'non') {
                $query->whereNull('user_id');
            }
        }

        if (isset($filters['active'])) {
            if ($filters['active'] === 'oui') {
                $query->whereHas('user', function($q) {
                    $q->where('etat', true);
                });
            } elseif ($filters['active'] === 'non') {
                $query->whereHas('user', function($q) {
                    $q->where('etat', false);
                });
            }
        }
        
        return Client::all();
    }
    public function create(array $clientData, array $userData = null): Client
    {
        DB::beginTransaction();
        try {
            // Créer le client
            $client = Client::create($clientData);

            // Créer l'utilisateur s'il existe
            if ($userData) {
                $user = User::create($userData);
                $user->client()->save($client);
            }

            DB::commit();
            return $client;
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    // public function create(array $data): Client
    // {
    //     return Client::create($data);
    // }

    public function find($id): Client
    {
        return Client::find($id);
    }

    public function ByTelephone($telephone): Client
    {
        return Client::where('telephone', $telephone)->first();
    }
}