<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @SWG\Definition(
 *     definition="Client",
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="nom", type="string"),
 *     @SWG\Property(property="prenom", type="string"),
 *     @SWG\Property(property="telephone", type="string"),
 *     @SWG\Property(property="user", ref="#/definitions/User")
 * )
 */

/**
 * @SWG\Definition(
 *     definition="ClientUser",
 *     type="object",
 *     @SWG\Property(property="client", ref="#/definitions/Client"),
 *     @SWG\Property(property="user", ref="#/definitions/User")
 * )
 */

/**
 * @SWG\Definition(
 *     definition="User",
 *     type="object",
 *     @SWG\Property(property="id", type="integer"),
 *     @SWG\Property(property="nom", type="string"),
 *     @SWG\Property(property="prenom", type="string"),
 *     @SWG\Property(property="email", type="string"),
 *     @SWG\Property(property="password", type="string")
 * )
 */
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
