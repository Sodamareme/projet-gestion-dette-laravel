<?php
// Chemin : app/Http/Controllers/ClientUserController.php
namespace App\Http\Controllers;

use App\Services\Client\ValidationInterface;
use App\Services\Client\FileManagementInterface;
use App\Services\Client\ClientEtUserServiceInterface;
use App\Services\Client\PhotoServiceInterface;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Facades\DB; // Importer DB correctement
use Endroid\QrCode\QrCode; // Correct import for Endroid QR code
use Endroid\QrCode\Writer\PngWriter; // Correct import for PngWriter

/**
 * @OA\Schema(
 *     schema="User",
 *     type="object",
 *     @OA\Property(property="id", type="integer", example=1),
 *     @OA\Property(property="nom", type="string", example="Doe"),
 *     @OA\Property(property="prenom", type="string", example="John"),
 *     @OA\Property(property="telephone", type="string", example="+1234567890"),
 *     @OA\Property(property="login", type="string", example="john.doe@example.com"),
 *     @OA\Property(property="mot_de_passe", type="string", example="password123"),
 *     @OA\Property(property="role_id", type="integer", example=1)
 * )
 */

/**
 * @OA\Schema(
 *     schema="Client",
 *     type="object",
 *     @OA\Property(property="user_id", type="integer", example=1),
 *     @OA\Property(property="surnom", type="string", example="JD"),
 *     @OA\Property(property="telephone", type="string", example="+1234567890"),
 *     @OA\Property(property="adresse", type="string", example="123 Main St"),
 *     @OA\Property(property="photo", type="string", example="base64encodedphoto")
 * )
 */
class ClientEtUserController extends Controller
{
    protected $validationService;
    protected $fileManagementService;
    protected $userService;
    private $photoService;

    public function __construct(
        ValidationInterface $validationService,
        FileManagementInterface $fileManagementService,
        ClientEtUserServiceInterface $userService,
        PhotoServiceInterface $photoService
    ) {
        $this->validationService = $validationService;
        $this->fileManagementService = $fileManagementService;
        $this->userService = $userService;
        $this->photoService = $photoService;
    }
 /**
     * @OA\Post(
     *     path="/api/user-client",
     *     summary="Create a new user and client",
     *     tags={"Client and User"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 type="object",
     *                 required={"nom", "prenom", "telephone", "login", "mot_de_passe", "role_id", "surnom"},
     *                 @OA\Property(property="nom", type="string", example="Doe"),
     *                 @OA\Property(property="prenom", type="string", example="John"),
     *                 @OA\Property(property="telephone", type="string", example="+1234567890"),
     *                 @OA\Property(property="login", type="string", example="john.doe@example.com"),
     *                 @OA\Property(property="mot_de_passe", type="string", example="password123"),
     *                 @OA\Property(property="role_id", type="integer", example=1),
     *                 @OA\Property(property="surnom", type="string", example="JD"),
     *                 @OA\Property(property="adresse", type="string", example="123 Main St"),
     *                 @OA\Property(property="photo", type="string", format="binary")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User and client created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="User and client created successfully"),
     *             @OA\Property(property="user", ref="#/components/schemas/User"),
     *             @OA\Property(property="client", ref="#/components/schemas/Client")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Role not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Role \"Client\" not found.")
     *         )
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal server error",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Failed to create user and client"),
     *             @OA\Property(property="exception", type="string")
     *         )
     *     )
     * )
     */

     public function createUserAndClient(Request $request)
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
     
         // Validation et préparation des données
         $validatedData = $validator->validated();
     
         if ($request->hasFile('photo')) {
             // Convertir la photo en base64
             $base64Photo = $this->photoService->convertAndStorePhoto($request->file('photo'));
             $validatedData['photo'] = $base64Photo;
         }
     
         $validatedData['role_id'] = $role->id;
     
         DB::beginTransaction();
     
         try {
             $user = $this->userService->store($validatedData);
     
             $clientData = [
                 'user_id' => $user->id,
                 'surnom' => $validatedData['surnom'],
                 'telephone' => $validatedData['telephone'],
                 'adresse' => $validatedData['adresse'] ?? null,
                 'photo' => $validatedData['photo'] ?? null,
             ];
     
             $client = $this->userService->createClient($clientData);
     
             // Generate QR code with user's name and phone
             $qrCodeData = $clientData['telephone'] . ', ' . $validatedData['prenom'] . ' ' . $validatedData['nom'];
             $qrCode = new QrCode($qrCodeData);
             $writer = new PngWriter();
             $result = $writer->write($qrCode);
     
             // Ensure the directory exists
             $qrCodeDirectory = storage_path('app/public/qrcodes');
             if (!is_dir($qrCodeDirectory)) {
                 mkdir($qrCodeDirectory, 0755, true); // Create directory with appropriate permissions
             }
     
             // Save the QR code image
             $qrCodePath = $qrCodeDirectory . '/' . $user->id . '.png';
             file_put_contents($qrCodePath, $result->getString());
     
             DB::commit();
     
             return response()->json([
                 'message' => 'User and client created successfully',
                 'user' => $user,
                 'client' => $client,
                 'qr_code_path' => asset('storage/qrcodes/' . $user->id . '.png')
             ], 201);
     
         } catch (\Exception $e) {
             DB::rollback();
             return response()->json(['error' => 'Failed to create user and client', 'exception' => $e->getMessage()], 500);
         }
     }
     public function getClientCard($id)
     {
         $client = \App\Models\Client::find($id);
     
         if (!$client) {
             return response()->json(['error' => 'Client not found'], 404);
         }
     
         $qr_code_path = asset('storage/qrcodes/' . $client->user_id . '.png');
     
         return response()->json([
             'client' => $client,
             'qr_code_url' => $qr_code_path
         ]);
     }
}
