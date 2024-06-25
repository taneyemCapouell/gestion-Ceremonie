<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api', ['except' => [
            'login',
            'register',
            'logout',
            'profile',
            'logout',
            'user',
            "updateUserPassword"
        ]]);
    }
    /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     tags={"auth"},
     *     summary="User Register",
     *     operationId="Register",
     *     description="Crée un nouvel utilisateur avec les détails fournis",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User model",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 @OA\Property(property="firstname", type="string"),
     *                 @OA\Property(property="email", type="string", format="email"),
     *                 @OA\Property(property="password", type="string", format="password"),
     *                 @OA\Property(property="password_confirmation", type="string", format="password"),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Unprocessable entry",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Bad request"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Not found"
     *     ),
     * )
     */
    // Register user controller
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors()->toJson(), 400);
        }

        if ($validator->passes()) {
            // User Creation
            $user = User::create([
                'firstname' => $request->firstname,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

            // Création du token d'API
            $token = $user->createToken('MyApp')->accessToken;

            return response()->json([
                "msg" => 'User successfully registered',
                "user" => $user,
                "token" => $token
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     tags={"auth"},
     *     summary="User login",
     *     operationId="login",
     *     description="Se connecetr en utilisant le mot de passe et une adresse email",
     *     @OA\RequestBody(
     *         description="User credentials",
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="email", format="email", description="User email"),
     *             @OA\Property(property="password", type="password", description="User password")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successful logged in.",
     *         @OA\JsonContent(
     *             @OA\Property(property="access_token", type="string", description="JWT token for authentication"),
     *             @OA\Property(property="token_type", type="string", description="Token type (Bearer)"),
     *             @OA\Property(property="expires_in", type="integer", description="Token expiration time in seconds")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials"
     *     )
     * )
     */
    // login user controller
    public function login(Request $request)
{
    $validator = Validator::make($request->all(), [
        'email' => ['required', 'string', 'email', 'max:40'],
        'password' => ['required', 'string', 'min:6'],
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'error' => $validator->errors(),
        ], 422);
    }

    $credentials = $request->only('email', 'password');
    $token = auth()->attempt($credentials);

    if (!$token) {
        return response()->json([
            'error' => 'Email ou mot de passe incorrect.',
        ] , 401);
    }

    $user = Auth::user();

    if ($user->status === 0) {
        Auth::logout();
        return response()->json([
            'error' => "Votre compte n'est pas activé.",
        ] , 402 );
    }

    $role = $user->role->name;

    return response()->json([
        "msg" => "Utilisateur connecté avec succès.",
        "access_token" => $token,
        "token_type" => 'Bearer',
        "user" => $user,
        "role" => $role,
    ])->cookie('jwt', $token);
}

    /**
     * @OA\Get(
     *     path="/api/auth/profile",
     *     tags={"auth"},
     *     summary="Get user profile",
     *     operationId="profile",
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="User profile retrieved successfully",
     *
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     )
     * )
     */
    //  get user informations
    public function profile(Request $request)
    {
        // Renvoyer les informations de l'utilisateur connecté
        return response()->json(['user' => Auth::user()], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/logout",
     *     tags={"auth"},
     *     summary="User Logout",
     *     description="Logout the authenticated user",
     *     security={{ "sanctum": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Successfully logged out",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     * )
     */
    // User logout
    public function logout()
    {
        auth()->logout();
        return response()->json([
            'msg' => 'User logged out'
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/auth/updateUserPassword",
     *     tags={"auth"},
     *     summary="Update User Password",
     *     description="Update the password of the authenticated user",
     *     security={{ "sanctum": {} }},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Password update details",
     *         @OA\JsonContent(
     *             required={"old_password", "new_password", "confirm_password"},
     *             @OA\Property(property="old_password", type="password", format="password", description="Old password"),
     *             @OA\Property(property="new_password", type="password", format="password", description="New password"),
     *             @OA\Property(property="confirm_password", type="password", format="password", description="Confirm new password"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password successfully updated",
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *     ),
     * )
     */
    // Updated user password
    public function updateUserPassword(Request $request)
    {


        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string',
            'confirm_password' => 'required|string'
        ]);

        // $user = Auth::user();

        if (!Hash::check($request->old_password, Auth::user()->password)) {
            return response()->json(
                [
                    'msg' => 'Incorrect password'
                ],
                401
            );
        }

        if ($request->new_password !== $request->confirm_password) {
            return response()->json([
                'msg' => 'The passwords do not match.'
            ]);
        }

        if (trim($request->new_password) !== trim($request->confirm_password)) {
            return response()->json([
                'msg' => 'The passwords do not match'
            ]);
        }

        $request->user()->update([
            'password' => Hash::make($request->new_password)
        ]);

        return response()->json(
            [
                'msg' => 'Your password has been updated successfull'
            ],
            201
        );
    }

    public function updateUserPassword1(Request $request)
    {
        // we retrieve the information of the connected user
        // $user = Auth::user();
        $request->validate([
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:6', // Ajoutez des règles de validation selon vos besoins
            'confirm_password' => 'required|string|same:new_password'
        ]);

        // Vérifier si l'ancien mot de passe correspond à celui enregistré en base de données
        if (!Hash::check($request->old_password,  auth()->user()->password)) {
            return response()->json([
                'error' => 'Ancien mot de passe incorrect'
            ]);
        }

        // Comparer les mots de passe
        if ($request->new_password !== $request->confirm_password) {
            return response()->json(['error' => 'Les mots de passe ne correspondent pas']);
        }

        // Mettre à jour le mot de passe de l'utilisateur

        // $user()->update([
        //     'password' => Hash::make($request->new_password),
        // ]);

        User::where('id', Auth::id())->update([Auth::user()->password, Hash::make($request->new_password)]);


        return response()->json(['message' => 'Mot de passe mis à jour avec succès']);
    }
}
