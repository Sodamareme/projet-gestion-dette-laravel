<?php

namespace App\Services\Client;

use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator; // Utilisez le contrat Validator

interface ValidationInterface
{
    public function validate(Request $request): Validator;
}