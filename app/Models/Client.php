<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Services\Client\UploadService;
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
 // Déclarez la relation avec le modèle User si elle existe
 public function user()
 {
     return $this->belongsTo(User::class);
 }
 
    
}
