<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Services\Client\PhotoServiceInterface;
use App\Services\Client\UserServiceInterface;
use App\Services\Client\ClientUserServiceInterface;
use App\Services\Client\FileManagementInterface;
use App\Services\Client\ValidationInterface;
use App\Services\Client\UploadService;
/**
 * @OA\Tag(
 *     name="ClientUser",
 *     description="Endpoints related to Client users"
 * )
 */

/**
 * @OA\Post(
 *     path="/api/clients/{client}/add-user",
 *     summary="Add a user to a client",
 *     tags={"ClientUser"},
 *     @OA\Parameter(
 *         name="client",
 *         in="path",
 *         required=true,
 *         @OA\Schema(type="integer"),
 *         description="ID of the client"
 *     ),
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             required={"nom", "prenom", "telephone", "login", "password"},
 *             @OA\Property(property="nom", type="string", example="John"),
 *             @OA\Property(property="prenom", type="string", example="Doe"),
 *             @OA\Property(property="telephone", type="string", example="1234567890"),
 *             @OA\Property(property="login", type="string", example="john.doe@example.com"),
 *             @OA\Property(property="password", type="string", format="password", example="password123"),
 *             @OA\Property(property="photo", type="string", format="binary")
 *         )
 *     ),
 *     @OA\Response(
 *         response=201,
 *         description="User created successfully",
 *         @OA\JsonContent(
 *             @OA\Property(property="message", type="string", example="User created successfully"),
 *             @OA\Property(property="user", ref="#/components/schemas/User")
 *         )
 *     ),
 *     @OA\Response(
 *         response=422,
 *         description="Validation errors",
 *         @OA\JsonContent(
 *             @OA\Property(property="errors", type="object")
 *         )
 *     ),
 *     @OA\Response(
 *         response=404,
 *         description="Role not found",
 *         @OA\JsonContent(
 *             @OA\Property(property="error", type="string", example="Role 'Client' not found.")
 *         )
 *     )
 * )
 */

 class ClientUserController extends Controller
 {
     protected $validationService;
     protected $fileManagementService;
     protected $userService;
     private $photoService;
 
     public function __construct(
         ValidationInterface $validationService,
         FileManagementInterface $fileManagementService,
         ClientUserServiceInterface $userService,
         PhotoServiceInterface $photoService
     ) {
         $this->validationService = $validationService;
         $this->fileManagementService = $fileManagementService;
         $this->userService = $userService;
         $this->photoService = $photoService;
     }
 
     public function store(Request $request)
     {
         // Validation des données
         $validator = $this->validationService->validate($request);
 
         if ($validator->fails()) {
             return response()->json(['errors' => $validator->errors()], 422);
         }
 
         // Récupération du rôle "Client"
         $role = Role::where('name', 'Client')->first();
 
         if (!$role) {
             return response()->json(['error' => 'Role "Client" not found.'], 404);
         }
 
         $validatedData = $validator->validated();
 
         if ($request->hasFile('photo')) {
             // Convertir la photo en base64
             $base64Photo = $this->photoService->convertAndStorePhoto($request->file('photo'));
             // Ajouter la photo en base64 aux données validées
             $validatedData['photo'] = $base64Photo;
         }
 
         // Ajouter le rôle à l'utilisateur
         $validatedData['role_id'] = $role->id;
 
         // Création de l'utilisateur
         $user = $this->userService->store($validatedData);
 
         return response()->json(['message' => 'User created successfully', 'user' => $user], 201);
     }
 }
