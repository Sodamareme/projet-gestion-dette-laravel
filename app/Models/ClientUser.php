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
/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     title="User",
 *     description="User model",
 *     properties={
 *         @OA\Property(property="id", type="integer", description="ID of the user"),
 *         @OA\Property(property="nom", type="string", description="Name of the user"),
 *         @OA\Property(property="prenom", type="string", description="Surname of the user"),
 *         @OA\Property(property="telephone", type="string", description="Phone number of the user"),
 *         @OA\Property(property="login", type="string", description="Login of the user"),
 *         @OA\Property(property="photo", type="string", description="Path to the user's photo"),
 *         @OA\Property(property="role_id", type="integer", description="Role ID of the user"),
 *     }
 * )
 */

/**
 * @OA\Schema(
 *     schema="Role",
 *     type="object",
 *     title="Role",
 *     description="Role model",
 *     properties={
 *         @OA\Property(property="id", type="integer", description="ID of the role"),
 *         @OA\Property(property="name", type="string", description="Name of the role"),
 *     }
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
