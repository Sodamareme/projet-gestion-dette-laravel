<?php
namespace App\Services\Client;
use App\Models\User;
use App\Models\Client;

 interface ClientUserServiceInterface
   {
      public function store(array $data): User;
   }
 