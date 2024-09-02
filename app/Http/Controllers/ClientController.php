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
     *     path="/clients/{id}/user",
     *     summary="Get client and associated user account",
     *     tags={"Clients"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer"),
     *         description="ID of the client"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Client with user details found",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", ref="#/components/schemas/ClientUser"),
     *             @OA\Property(property="message", type="string", example="Client with user details found")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Client not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="data", type="null"),
     *             @OA\Property(property="message", type="string", example="Object not found")
     *         )
     *     )
     * )
     */
    /**
 * @OA\Schema(
 *     schema="ClientUser",
 *     type="object",
 *     required={"nom", "prenom", "telephone"},
 *     @OA\Property(property="nom", type="string", example="Doe"),
 *     @OA\Property(property="prenom", type="string", example="John"),
 *     @OA\Property(property="telephone", type="string", example="1234567890"),
 *     @OA\Property(property="adresse", type="string", example="123 Main St"),
 *     @OA\Property(property="photo", type="string", format="binary")
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
     *     path="/clients",
     *     summary="List clients",
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
 *     path="/clients/telephone",
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
