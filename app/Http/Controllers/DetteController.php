<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dette;
use App\Models\Client;
class DetteController extends Controller
{

public function createDette(Request $request)
{
    $request->validate([
        'montant' => 'required|numeric',
        'montantDu' => 'required|numeric',
        'montantRestant' => 'required|numeric',
        'idClient' => 'required|exists:clients,id',
    ]);

    $dette = Dette::create($request->all());

    return response()->json([
        'data' => $dette,
        'message' => 'Dette créée avec succès'
    ], 201);
}


public function getDettes($clientId)
{
    $client = Client::find($clientId);

    if (!$client) {
        return response()->json([
            'data' => null,
            'message' => 'Client non trouvé'
        ], 404);
    }

    $dettes = $client->dettes;

    return response()->json([
        'data' => $dettes,
        'message' => 'Dettes récupérées avec succès'
    ], 200);
}



}
