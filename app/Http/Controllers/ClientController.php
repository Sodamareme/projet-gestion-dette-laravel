<?php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
/**
 * @OA\Tag(
 *     name="Clients",
 *     description="API endpoints for managing clients"
 * )
 */

/**
 * @OA\Schema(
 *     schema="Client",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="surnom", type="string", example="John Doe"),
 *     @OA\Property(property="telephone", type="string", example="1234567890"),
 *     @OA\Property(property="adresse", type="string", example="123 Main St"),
 *     @OA\Property(property="photo", type="string", example="path/to/photo.jpg"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2023-01-01T00:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2023-01-01T00:00:00Z")
 * )
 */


class ClientController extends Controller
{
/**
 * @OA\Post(
 *     path="/clients",
 *     summary="Create a new client",
 *     tags={"Clients"},
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"surnom", "telephone"},
 *             @OA\Property(property="surnom", type="string", example="John Doe"),
 *             @OA\Property(property="telephone", type="string", example="1234567890"),
 *             @OA\Property(property="adresse", type="string", example="123 Main St"),
 *             @OA\Property(property="photo", type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="Client created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="client", ref="#/components/schemas/Client")
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Invalid input"
 *     )
 * )
 */
    public function createClient(Request $request)
    {
        // Validation des données d'entrée
        $request->validate([
            'surnom' => 'required|string|max:255',
            'telephone' => 'required|string|max:15|unique:clients',
            'adresse' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        // Stockage de la photo si présente
        $photoPath = $request->file('photo') ? $request->file('photo')->store('photos', 'public') : null;

        // Création du client
        $client = Client::create([
            'surnom' => $request->surnom,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'photo' => $photoPath,
        ]);

        // Retour de la réponse JSON avec le client créé
        return response()->json([
            'client' => $client,
        ], 201);
 }
 /**
 * @OA\Get(
 *     path="/clients/{id}",
 *     summary="Obtenir les informations d'un client par ID",
 *     description="Récupère les informations d'un client en utilisant son ID.",
 *     tags={"Clients"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du client",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client trouvé avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1, description="ID du client"),
 *                 @OA\Property(property="name", type="string", example="John Doe", description="Nom du client"),
 *                 @OA\Property(property="email", type="string", example="john.doe@example.com", description="Email du client"),
 *                 @OA\Property(property="phone", type="string", example="123-456-7890", description="Numéro de téléphone du client")
 *             ),
 *             @OA\Property(property="message", type="string", example="Client trouvé", description="Message de réponse")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Client non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Objet non trouvé", description="Message de réponse")
 *         )
 *     )
 * )
 */
 public function showClient($id)
 {
     // Trouver le client par ID
     $client = Client::find($id);

     // Si le client n'existe pas, retour d'une réponse 404
     if (!$client) {
         return response()->json([
             'data' => null,
             'message' => 'Objet non trouvé'
         ], 404);
     }

     // Retourner les informations du client
     return response()->json([
         'data' => $client,
         'message' => 'Client trouvé'
     ], 200);
 }
/**
 * @OA\Post(
 *     path="/clients/{id}/dettes",
 *     summary="Obtenir les dettes d'un client par ID",
 *     description="Récupère les informations d'un client ainsi que ses dettes en utilisant son ID.",
 *     tags={"Clients"},
 *     @OA\Parameter(
 *         name="id",
 *         in="path",
 *         required=true,
 *         description="ID du client",
 *         @OA\Schema(
 *             type="integer"
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Client et dettes trouvés avec succès",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="data",
 *                 type="object",
 *                 @OA\Property(property="id", type="integer", example=1, description="ID du client"),
 *                 @OA\Property(property="name", type="string", example="John Doe", description="Nom du client"),
 *                 @OA\Property(property="email", type="string", example="john.doe@example.com", description="Email du client"),
 *                 @OA\Property(property="phone", type="string", example="123-456-7890", description="Numéro de téléphone du client"),
 *                 @OA\Property(
 *                     property="dettes",
 *                     type="array",
 *                     @OA\Items(
 *                         type="object",
 *                         @OA\Property(property="id", type="integer", example=1, description="ID de la dette"),
 *                         @OA\Property(property="amount", type="number", format="float", example=100.00, description="Montant de la dette"),
 *                         @OA\Property(property="due_date", type="string", format="date", example="2024-12-31", description="Date d'échéance de la dette")
 *                     )
 *                 )
 *             ),
 *             @OA\Property(property="message", type="string", example="Client et dettes trouvés", description="Message de réponse")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Client non trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="data", type="null"),
 *             @OA\Property(property="message", type="string", example="Objet non trouvé", description="Message de réponse")
 *         )
 *     )
 * )
 */

 public function showClientDebts($id)
 {
     // Trouver le client par ID
     $client = Client::with('dettes')->find($id);

     // Si le client n'existe pas, retour d'une réponse 404
     if (!$client) {
         return response()->json([
             'data' => null,
             'message' => 'Objet non trouvé'
         ], 404);
     }

     // Retourner les informations du client et ses dettes
     return response()->json([
         'data' => $client, // Les dettes sont incluses automatiquement grâce à la relation 'dettes'
         'message' => 'Client trouvé'
     ], 200);
 }
    /**
     * @OA\Post(
     *     path="/clients/{id}/user",
     *     summary="Afficher les informations du client avec son utilisateur associé",
     *     description="Récupère les informations d'un client ainsi que les informations de l'utilisateur associé en utilisant l'ID du client.",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID du client",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client trouvé avec succès",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="id", type="integer", example=1, description="ID du client"),
     *                 @OA\Property(property="name", type="string", example="John Doe", description="Nom du client"),
     *                 @OA\Property(
     *                     property="user",
     *                     type="object",
     *                     @OA\Property(property="id", type="integer", example=1, description="ID de l'utilisateur associé"),
     *                     @OA\Property(property="username", type="string", example="johndoe", description="Nom d'utilisateur"),
     *                     @OA\Property(property="email", type="string", example="john.doe@example.com", description="Adresse e-mail de l'utilisateur")
     *                 ),
     *                 @OA\Property(property="message", type="string", example="Client trouvé", description="Message de réponse")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client non trouvé",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Objet non trouvé", description="Message de réponse")
     *         )
     *     )
     * )
     */

    public function showClientWithUser($id)
    {
        // Trouver le client par ID avec l'utilisateur associé
        $client = Client::with('user')->find($id);

        // Si le client n'existe pas, retour d'une réponse 404
        if (!$client) {
            return response()->json([
                'data' => null,
                'message' => 'Objet non trouvé'
            ], 404);
        }

        // Retourner les informations du client et de l'utilisateur associé
        return response()->json([
            'data' => $client, // L'utilisateur est inclus automatiquement grâce à la relation 'user'
            'message' => 'Client trouvé'
        ], 200);
    }
   /**
     * @OA\Get(
     *     path="/api/v1/clients",
     *     summary="lister tout les clients ou lister les clients avec compte ou sans compte et lister client avec compte active ou non",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="comptes",
     *         in="query",
     *         description="Filter clients by comptes",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="A list of clients",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Client")
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid request"
     *     )
     * )
     */
    // Lister les clients
    public function listClients(Request $request)
    {
        // Récupérer les paramètres de la requête
        $comptes = $request->query('comptes');
        $active = $request->query('active');

        // Définir la requête de base pour récupérer les clients
        $query = Client::query();

        // Appliquer les filtres selon les paramètres de la requête
        if ($comptes === 'non') {
            $query->whereHas('user');
        } elseif ($comptes === 'oui') {
            $query->whereDoesntHave('user');
        }

        if ($active === 'non') {
            $query->whereHas('user', function($query) {
                $query->whereNotNull('id'); // Assurez-vous que cette condition vérifie bien l'activité de l'utilisateur
            });
        } elseif ($active === 'oui') {
            $query->whereDoesntHave('user');
        }

        // Récupérer les clients
        $clients = $query->get();

        // Retourner les informations des clients
        return response()->json([
            'clients' => $clients,
            'message' => 'Liste des clients récupérée avec succès'
        ], 200);
    }
   /**
 * @OA\Post(
 *     path="/api/v1/clients/telephone",
 *     summary="Rechercher un client par numéro de téléphone",
 *     tags={"Clients"},
 *     @OA\Parameter(
 *         name="telephone",
 *         in="query",
 *         required=true,
 *         @OA\Schema(type="string"),
 *         description="Numéro de téléphone du client"
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Clients trouvés",
 *         @OA\JsonContent(
 *             @OA\Property(property="clients", type="array", @OA\Items(ref="#/components/schemas/Client")),
 *             @OA\Property(property="message", type="string", example="Clients trouvés")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Aucun client trouvé",
 *         @OA\JsonContent(
 *             @OA\Property(property="clients", type="array", @OA\Items(ref="#/components/schemas/Client")),
 *             @OA\Property(property="message", type="string", example="Aucun client trouvé")
 *         )
 *     )
 * )
 */
    public function searchClientByPhone(Request $request)
    {
        // Validation des données d'entrée
        $request->validate([
            'telephone' => 'required|string|max:15',
        ]);

        // Récupération du numéro de téléphone depuis la requête
        $telephone = $request->input('telephone');

        // Recherche des clients avec le numéro de téléphone donné
        $clients = Client::where('telephone', $telephone)->get();

        // Retourner les informations des clients trouvés
        return response()->json([
            'clients' => $clients,
            'message' => $clients->isEmpty() ? 'Aucun client trouvé' : 'Clients trouvés'
        ], $clients->isEmpty() ? 404 : 200);
    }

}
