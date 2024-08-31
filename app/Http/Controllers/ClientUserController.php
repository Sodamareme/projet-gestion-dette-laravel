<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class ClientUserController extends Controller
{
    public function store(Request $request)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'telephone' => 'required|string|max:20',
            'login' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048', // Validation pour l'image
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Récupération du rôle "Client" à partir de la table `roles`
        $role = Role::where('name', 'Client')->first();

        // Vérifier si le rôle "Client" existe
        if (!$role) {
            return response()->json(['error' => 'Role "Client" not found.'], 404);
        }

        // Création de l'utilisateur
        $user = User::create([
            'nom' => $request->input('nom'),
            'prenom' => $request->input('prenom'),
            'telephone' => $request->input('telephone'),
            'photo' => $request->file('photo') ? $request->file('photo')->store('photos', 'public') : null,
            'login' => $request->input('login'),
            'password' => Hash::make($request->input('password')),
            'role_id' => $role->id, // Ajoute l'id du rôle "Client"
        ]);

        return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
    }
}
