<?php
namespace App\Services\Client;
use App\Models\User;
use App\Services\Client\ClientUserServiceInferface;
use Illuminate\Support\Facades\Hash;
use App\Facades\UploadFacade;
class ClientUserServiceImpl implements ClientUserServiceInterface
{
   public function store(array $data): User
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
}