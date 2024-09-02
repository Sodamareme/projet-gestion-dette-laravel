<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Article;
use Illuminate\Support\Facades\Auth;
/**
 * @OA\Tag(
 *     name="Articles",
 *     description="API endpoints for managing clients"
 * )
 */
class ArticleController extends Controller
{
  /**
     * @OA\Post(
     *     path="/api/articles",
     *     summary="Créer un nouvel article",
     *     tags={"Articles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"libelle", "prix", "quantiteStock"},
     *                 @OA\Property(property="libelle", type="string", example="Article Exemplar"),
     *                 @OA\Property(property="prix", type="number", format="float", example=99.99),
     *                 @OA\Property(property="quantiteStock", type="integer", example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article créé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Article enregistré avec succès"),
     *             @OA\Property(property="article", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="libelle", type="string", example="Article Exemplar"),
     *                 @OA\Property(property="prix", type="number", format="float", example=99.99),
     *                 @OA\Property(property="quantiteStock", type="integer", example=10)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", additionalProperties={"type":"array", "items":{"type":"string"}}),
     *             @OA\Property(property="message", type="string", example="Erreur de validation")
     *         )
     *     )
     * )
     */
    public function store(Request $request)
    {
        // $this->authorize('access', Article::class);
        // Récupérer l'utilisateur connecté
        $user = Auth::user();
 
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
       /**
     * @OA\Get(
     *     path="/api/v1/articles",
     *     summary="Lister tous les articles",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="disponible",
     *         in="query",
     *         description="Filtrer les articles en fonction de leur disponibilité",
     *         required=false,
     *         @OA\Schema(type="string", enum={"oui", "non"})
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Liste des articles",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="array", 
     *                 @OA\Items(type="object",
     *                     @OA\Property(property="id", type="integer", example=1),
     *                     @OA\Property(property="libelle", type="string", example="Article Exemplar"),
     *                     @OA\Property(property="prix", type="number", format="float", example=99.99),
     *                     @OA\Property(property="quantiteStock", type="integer", example=10)
     *                 )
     *             ),
     *             @OA\Property(property="message", type="string", example="Liste des articles")
     *         )
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Aucun article trouvé"
     *     )
     * )
     */
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
  /**
     * @OA\Get(
     *     path="/api/v1/articles/{id}",
     *     summary="Obtenir un article par ID",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="libelle", type="string", example="Article Exemplar"),
     *                 @OA\Property(property="prix", type="number", format="float", example=99.99),
     *                 @OA\Property(property="quantiteStock", type="integer", example=10)
     *             ),
     *             @OA\Property(property="message", type="string", example="Article trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Article non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Objet non trouvé")
     *         )
     *     )
     * )
     */
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
 /**
     * @OA\Post(
     *     path="/api/v1/articles/libelle",
     *     summary="Obtenir un article par libellé",
     *     tags={"Articles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"libelle"},
     *                 @OA\Property(property="libelle", type="string", example="Article Exemplar")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=200),
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="libelle", type="string", example="Article Exemplar"),
     *                 @OA\Property(property="prix", type="number", format="float", example=99.99),
     *                 @OA\Property(property="quantiteStock", type="integer", example=10)
     *             ),
     *             @OA\Property(property="message", type="string", example="Article trouvé")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Article non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Objet non trouvé")
     *         )
     *     )
     * )
     */
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
      /**
     * @OA\Patch(
     *     path="/api/v1/articles/{id}",
     *     summary="Mettre à jour la quantité en stock d'un article par ID",
     *     tags={"Articles"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID de l'article",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"qteStock"},
     *                 @OA\Property(property="qteStock", type="integer", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quantité en stock mise à jour",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="libelle", type="string", example="Article Exemplar"),
     *                 @OA\Property(property="prix", type="number", format="float", example=99.99),
     *                 @OA\Property(property="quantiteStock", type="integer", example=15)
     *             ),
     *             @OA\Property(property="message", type="string", example="Quantité en stock mise à jour")
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Article non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="status", type="integer", example=411),
     *             @OA\Property(property="data", type="object"),
     *             @OA\Property(property="message", type="string", example="Objet non trouvé")
     *         )
     *     )
     * )
     */
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
 /**
     * @OA\Post(
     *     path="/api/v1/articles/stock",
     *     summary="Mettre à jour la quantité en stock d'un article par ID",
     *     tags={"Articles"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(type="object", @OA\Property(property="articles", type="array", @OA\Items(ref="UpdateStockRequest")))
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Quantité mise à jour avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Quantité mise à jour avec succès"),
     *             @OA\Property(property="article", type="object", 
     *                 @OA\Property(property="id", type="integer", example=1),
     *                 @OA\Property(property="libelle", type="string", example="Article Exemplar"),
     *                 @OA\Property(property="prix", type="number", format="float", example=99.99),
     *                 @OA\Property(property="quantiteStock", type="integer", example=15)
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=411,
     *         description="Erreur de validation",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object", additionalProperties={"type":"array", "items":{"type":"string"}}),
     *             @OA\Property(property="message", type="string", example="Erreur de validation")
     *         )
     *     )
     * )
     */
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
