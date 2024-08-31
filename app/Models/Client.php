<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // Définir les attributs qui peuvent être attribués en masse
    protected $fillable = [
        'nom',
        'prenom',
        'telephone',
        'photo',
        'login',
        'password',
        'surnom', // Ajoutez ici le champ surnom
    ];
}
