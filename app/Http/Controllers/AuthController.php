<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
class AuthController extends Controller
{
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

