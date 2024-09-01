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

    // Méthode pour obtenir un article par ID
    public function showById($id)
    {
        $article = Article::find($id);

        if (!$article) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ], 411);
        }

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'Article trouvé'
        ], 200);
    }

    // Méthode pour obtenir un article par libellé
    public function showByLibelle(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'libelle' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'message' => 'Erreur de validation'], 411);
        }

        $libelle = $request->input('libelle');
        $article = Article::where('libelle', $libelle)->first();

        if (!$article) {
            return response()->json([
                'status' => 411,
                'data' => null,
                'message' => 'Objet non trouvé'
            ], 411);
        }

        return response()->json([
            'status' => 200,
            'data' => $article,
            'message' => 'Article trouvé'
        ], 200);
    }
    public function updateStockById(Request $request, $id)
    {
        // Validation des données
        $validator = Validator::make($request->all(), [
            'qteStock' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['data' => null, 'message' => 'Validation échouée'], 411);
        }

        // Trouver l'article par ID
        $article = Article::find($id);

        if (!$article) {
            return response()->json(['data' => null, 'message' => 'Objet non trouvé'], 411);
        }

        // Mise à jour de la quantité en stock
        $article->quantiteStock = $request->input('qteStock');
        $article->save();

        return response()->json([
            'data' => $article,
            'message' => 'qte stock mis a jour'
        ], 200);
    }

    public function updateStockByIds(Request $request)
    {
        // Validation des données
    $validator = Validator::make($request->all(), [
        'articles' => 'required|array',
        'articles.*.id' => 'required|exists:articles,id',
        'articles.*.qteStock' => 'required|numeric|min:0',
    ]);

    if ($validator->fails()) {
        return response()->json(['articles' => null, 'message' => 'Validation échouée'], 411);
    }

    $data = $request->input('articles'); // Ajustez en fonction de la façon dont vous récupérez les données d'entrée
    $success = [];
    
    
$errors = [];

    // Assurez-vous que $data est un tableau
    if (is_array($data)) {
        foreach ($data as $item) {
            // Trouver l'article par ID
            $article = Article::find($item['id']);

            if ($article) {
                
     
// Mise à jour de la quantité en stock
                $article->quantiteStock = $item['qteStock'];
                $article->save();
                $success[] = $article;
            } else {
                $errors[] = [
                    'id' => $item['id'],
                    'message' => 'Article non trouvé'
                ];
            }
        }
    } else {
        // Gérer le cas où $data n'est pas un tableau
        return response()->json(['error' => 'Format de données invalide'], 400);
    }

    return response()->json([
        'success' => $success,
        'errors' => $errors
    ], 200);
    }
}
