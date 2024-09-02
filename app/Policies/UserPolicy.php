<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Article;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Auth\Access\Response;
class UserPolicy
{
   public function isAdmin(User $user){
    return $user->role === 'Admin';
   }
   public function isBoutiquier(User $user){
    return $user->role === 'Boutiquier';
   }
   public function isClient(User $user){
    return $user->role === 'Client';
   }
}
