<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
/**
 * @OA\Tag(
 *     name="Clients",
 *     description="API endpoints for managing clients"
 * )
 */

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     required={"id", "nom", "prenom", "telephone", "photo", "login", "role_id"},
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nom", type="string", example="John"),
 *     @OA\Property(property="prenom", type="string", example="Doe"),
 *     @OA\Property(property="telephone", type="string", example="123456789"),
 *     @OA\Property(property="photo", type="string", example="photos/photo.jpg"),
 *     @OA\Property(property="login", type="string", example="user@example.com"),
 *     @OA\Property(property="role_id", type="integer", example=1)
 * )
 */

class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/register",
     *     tags={"Auth"},
     *     summary="Register a new user",
     *     operationId="registerUser",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"nom", "prenom", "telephone", "photo", "login", "password"},
     *                 @OA\Property(property="nom", type="string", example="John"),
     *                 @OA\Property(property="prenom", type="string", example="Doe"),
     *                 @OA\Property(property="telephone", type="string", example="123456789"),
     *                 @OA\Property(property="photo", type="string", example="photos/photo.jpg"),
     *                 @OA\Property(property="login", type="string", example="user@example.com"),
     *                 @OA\Property(property="password", type="string", example="password")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=201),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="user", ref="#/components/schemas/User"),
     *                 @OA\Property(property="token", type="string", example="your-jwt-token")
     *             ),
     *             @OA\Property(property="message", type="string", example="User Registered Successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation Error",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=400),
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Validation Error")
     *         )
     *     )
     * )
     */

    public function register(Request $request)
    {
        $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:15|unique:users',
            'photo' => 'nullable|image|max:2048',
            'login' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);
       // Gérer l'upload de la photo si nécessaire
         $photoPath = $request->file('photo') ? $request->file('photo')->store('photos', 'public') : null;

         // Créer l'utilisateur
         $user = User::create([
            'nom' => $request->nom,
            'prenom' => $request->prenom,
            'telephone' => $request->telephone,
            'photo' => $photoPath,
            'login' => $request->login,
            'password' => Hash::make($request->password),
            'role_id' => $request->role_id, // Assigner le rôle par ID
        ]);
          // Assigner le rôle à l'utilisateur
        $user->assignRole($request->role);

        return response()->json([
            $user = User::find(1),// Trouvez l'utilisateur par ID
      $token = $user->createToken('Token Name')->accessToken,
            // 'user' => $user,
            // 'token' => $user->createToken('API Token')->accessToken,
        ], 201);
    }
  
/**
 * @OA\Post(
 *     path="/api/login",
 *     summary="Login user",
 *     tags={"Auth"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"login", "password"},
 *             @OA\Property(property="login", type="string", example="user@example.com"),
 *             @OA\Property(property="password", type="string", example="password"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Successful login",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=200),
 *             @OA\Property(property="data", type="object",
 *                 @OA\Property(property="token", type="string", example="your-jwt-token")
 *             ),
 *             @OA\Property(property="message", type="string", example="Login Successful"),
 *         ),
 *     ),
 *     @OA\Response(
 *         response=401,
 *         description="Invalid credentials",
 *         @OA\JsonContent(
 *             @OA\Property(property="status", type="integer", example=401),
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Invalid Credentials"),
 *         ),
 *     )
 * )
 */
    
    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string',
        ]);
    
        // Assurez-vous que vous utilisez le champ correct pour l'identifiant de l'utilisateur
        $credentials = $request->only('login', 'password');
    
        if (Auth::attempt($credentials)) {
            /**  @var \App\Models\User $user **/
            $user = Auth::user(); // Obtenez l'utilisateur actuellement authentifié
            $token = $user->createToken('api_token')->accessToken; // Utilisez accessToken pour obtenir le jeton
            return response()->json([
                'status' => 200,
                'data' => ['token' => $token],
                'message' => 'Login Successful'
            ]);
        } else {
            return response()->json([
                'status' => 401,
                'data' => null,
                'message' => 'Invalid Credentials'
            ], 401);
        }
    }
/**
 * @OA\Get(
 *     path="/api/usersAll",
 *     summary="Liste tous les utilisateurs",
 *     tags={"Users"},
 *     @OA\Response(
 *         response=200,
 *         description="Liste des utilisateurs récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Liste des utilisateurs récupérée avec succès"),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=204,
 *         description="Aucun utilisateur trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Aucun utilisateur trouvé"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function listUsers()
    {
        // Récupérer tous les utilisateurs
        $users = User::all();
    
        // Vérifier si la liste des utilisateurs est vide
        if ($users->isEmpty()) {
            return response()->json([
                'data' => null,
                'message' => 'Aucun utilisateur trouvé'
            ], 200);
        }
    
        // Retourner la liste des utilisateurs
        return response()->json([
            'data' => $users,
            'message' => 'Liste des utilisateurs récupérée avec succès'
        ], 200);
    }
    /**
 * @OA\Get(
 *     path="/api/users",
 *     summary="Liste les utilisateurs par rôle",
 *     tags={"Users"},
 *     @OA\Parameter(
 *         name="role",
 *         in="query",
 *         required=true,
 *         description="Nom du rôle pour filtrer les utilisateurs",
 *         @OA\Schema(type="string")
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Liste des utilisateurs récupérée avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Liste des utilisateurs récupérée avec succès"),
 *             @OA\Property(property="data", type="array", @OA\Items(ref="#/components/schemas/User"))
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Le rôle est requis",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Le rôle est requis."),
 *             @OA\Property(property="data", type="null")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Rôle non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="Rôle non trouvé"),
 *             @OA\Property(property="data", type="null")
 *         )
 *     )
 * )
 */
    public function listUsersByRole(Request $request)
    {
        // Récupérer le rôle depuis les paramètres de la requête
        $roleName = $request->query('role');

        if (!$roleName) {
            return response()->json([
                'data' => null,
                'message' => 'Le rôle est requis.'
            ], 400);
        }

        // Trouver le rôle correspondant
        $role = \App\Models\Role::where('name', $roleName)->first();

        if (!$role) {
            return response()->json([
                'data' => null,
                'message' => 'Rôle non trouvé'
            ], 404);
        }

        // Récupérer les utilisateurs avec le rôle spécifié
        $users = User::where('role_id', $role->id)->get();

        // Retourner la liste des utilisateurs ou null si la liste est vide
        return response()->json([
            'data' => $users->isEmpty() ? null : $users,
            'message' => $users->isEmpty() ? 'Aucun utilisateur trouvé avec ce rôle' : 'Liste des utilisateurs récupérée avec succès'
        ], 200);
    }
    
}
// public function login(Request $request)
// {
//     // Validation des entrées
//     $request->validate([
//         'login' => 'required',
//         'password' => 'required',
//     ]);

//     // Trouver l'utilisateur
//     $user = User::where('login', $request->login)->first();

//     // Vérifier le mot de passe
//     if (!$user || !Hash::check($request->password, $user->password)) {
//         return response()->json(['message' => 'The provided credentials are incorrect.'], 401);
//     }

//     // Créer un token pour l'utilisateur
//     $token = $user->createToken('API Token')->accessToken;

//     // Retourner la réponse JSON
//     return response()->json([
//         'user' => $user,
//         'token' => $token,
//     ]);
// }

