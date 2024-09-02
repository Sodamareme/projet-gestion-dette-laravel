<?php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
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
    // lister les clients
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
