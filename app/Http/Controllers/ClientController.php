<?php
namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClientController extends Controller
{
    public function createClient(Request $request)
    {
        $request->validate([
            'surnom' => 'required|string|max:255',
            'telephone' => 'required|string|max:15|unique:clients',
            'adresse' => 'nullable|string|max:255',
            'photo' => 'nullable|image|max:2048',
        ]);

        $photoPath = $request->file('photo') ? $request->file('photo')->store('photos', 'public') : null;

        $client = Client::create([
            'surnom' => $request->surnom,
            'telephone' => $request->telephone,
            'adresse' => $request->adresse,
            'photo' => $photoPath,
        ]);

        return response()->json([
            'client' => $client,
        ], 201);
    }
}
