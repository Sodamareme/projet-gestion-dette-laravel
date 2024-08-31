<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;

class ArticleController extends Controller
{
    public function store(Request $request)
    {
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
 
        // // Vérifier que l'utilisateur est bien un Boutiquier
        // if (!$user || !$user->hasRole('Boutiquier')) {
        //     return response()->json(['message' => 'Unauthorized'], 403);
        // }

        // Validation des données
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string|max:255|unique:articles',
            'prix' => 'required|numeric|min:0',
            'quantiteStock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => 'Erreur de validation'], 411);
        }

        // Création de l'article
        $article = Article::create([
            'libelle' => $request->input('libelle'),
            'prix' => $request->input('prix'),
            'quantiteStock' => $request->input('quantiteStock'),
        ]);

        return response()->json(['message' => 'Article enregistré avec succès', 'article' => $article], 201);
    }
      // Méthode pour lister tous les articles
    public function index(Request $request)
    {
        // Récupérer le paramètre 'disponible'
        $disponible = $request->query('disponible');

        // Filtrer les articles en fonction du paramètre 'disponible'
        if ($disponible === 'oui') {
            // Articles dont la quantité en stock est > 0
            $articles = Article::where('quantiteStock', '>', 0)->get();
        } elseif ($disponible === 'non') {
            // Articles dont la quantité en stock est = 0
            $articles = Article::where('quantiteStock', '=', 0)->get();
        } else {
            // Si le paramètre 'disponible' est absent ou incorrect, retourner tous les articles
            $articles = Article::all();
        }

        // Vérifier s'il y a des articles et retourner la réponse appropriée
        if ($articles->isEmpty()) {
            return response()->json([
                'status' => 200,
                'data' => null,
                'message' => 'Pas Articles'
            ], 200);
        }

        return response()->json([
            'status' => 200,
            'data' => $articles,
            'message' => 'Liste des articles'
        ], 200);
    }
}
