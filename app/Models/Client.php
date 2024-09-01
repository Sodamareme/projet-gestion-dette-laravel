<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    // Définir les attributs qui peuvent être attribués en masse
    protected $fillable = [
        'surnom',
        'telephone',
        'photo',
    ];
    public function dettes()
{
    return $this->hasMany(Dette::class, 'client_id');
}
    
}
