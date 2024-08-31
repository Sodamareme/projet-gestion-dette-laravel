<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class ClientUser extends Model
{

    use HasFactory;

    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'photo',
        'login',
        'password',
        'client_id', // Assure-toi que `client_id` est bien dans $fillable
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];
}
