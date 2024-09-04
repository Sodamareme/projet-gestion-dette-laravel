<?php

namespace App\Services\Client;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator; // Utilisez le contrat Validator
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use App\Services\Client\ValidationInterface;
class ValidationService implements ValidationInterface
{
    public function validate(Request $request): Validator
    {
        return ValidatorFacade::make($request->all(), [
            'nom' => 'required|string|max:255',
        'prenom' => 'required|string|max:255',
        'telephone' => 'required|string|max:15',
        'login' => 'required|string|email|max:255|unique:users',
        'password' => 'required|string|min:8|confirmed',
        'role_id' => 'required|exists:roles,id',
        'surnom' => 'required|string|max:255', // Assurez-vous que 'surnom' est requis
        'adresse' => 'nullable|string|max:255',
        'photo' => 'nullable|image|mimes:jpg,png,jpeg|max:2048', // Assurez-vous que 'photo' est optionnelle
        ]);
    }
}
