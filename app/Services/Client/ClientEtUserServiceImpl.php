<?php
// Chemin : app/Services/Client/ClientUserService.php
namespace App\Services\Client;

use App\Models\User;
use App\Models\Client;
use Illuminate\Support\Facades\Hash; 
class ClientEtUserServiceImpl implements ClientEtUserServiceInterface
{
    public function store(array $data)
    {
        $photoPath = $data['photo'] ?? null;
          return User::create([
              'nom' => $data['nom'],
              'prenom' => $data['prenom'],
              'telephone' => $data['telephone'],
             'login' => $data['login'],
               'photo' => $photoPath,
              'password' => Hash::make($data['password']),
              'role_id' => $data['role_id'],
          ]);
      }

    public function createClient(array $data)
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
}
