<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator as FacadesValidator;

class UserController extends Controller
{

    /**
     * @OA\Post(
     *     path="/api/user/store",
     *     tags={"user"},
     *     summary="Register a new User",
     *     description="Create a new user with the provided details",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User registration details",
     *         @OA\JsonContent(
     *             required={"firstname", "email", "password", "password_confirmation", "gender", "role_id"},
     *             @OA\Property(property="firstname", type="string", description="User firstname"),
     *             @OA\Property(property="lastname", type="string", description="User lastname"),
     *             @OA\Property(property="email", type="string", format="email", description="User email"),
     *             @OA\Property(property="gender", type="string", description="User gender"),
     *             @OA\Property(property="phone", type="number", description="User phone"),
     *             @OA\Property(property="profile_image", type="string", format="file", description="User profile_image"),
     *             @OA\Property(property="role_id", type="string", description="User role_id"),
     *             @OA\Property(property="password", type="string", format="password", description="User password"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", description="User password confirmation"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User successfully registered",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Utilisateur créé avec succès"),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Erreur de validation"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "firstname": {"Le champ firstname est requis."},
     *                     "email": {"Le champ email est requis.", "Le champ email doit être une adresse email valide."},
     *                     "gender": {"Le champ gender est requis."},
     *                     "password": {"Le champ password est requis.", "Veillez confirmer le mot de passe.", "Le mot de passe doit avoir au moins 6 caractères."},
     *                     "role_id": {"Le champ role est requis."},
     *                 }
     *             ),
     *         ),
     *     )
     * )
     *
     * @OA\Schema(
     *     schema="User",
     *     required={"firstname", "email", "password", "password_confirmation", "gender", "role_id"},
     *     @OA\Property(property="firstname", type="string", description="User firstname"),
     *     @OA\Property(property="lastname", type="string", description="User lastname"),
     *     @OA\Property(property="email", type="string", format="email", description="User email"),
     *     @OA\Property(property="gender", type="string", description="User gender"),
     *     @OA\Property(property="status", type="boolean", description="User status"),
     *     @OA\Property(property="phone", type="number", description="User phone"),
     *     @OA\Property(property="profile_image", type="string", format="file", description="User profile_image"),
     *     @OA\Property(property="role_id", type="string", description="User role_id"),
     *     @OA\Property(property="password", type="string", format="password", description="User password"),
     * )
     */

    // Store user
    public function store(Request $request)
    {
        $validation = FacadesValidator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'string|max:50',
            'gender' => 'string|max:10',
            'user_code_confirmation' => 'string|max:4|min:4',
            'phone'  => 'integer|min:9|required|unique:users',
            'email' => 'required|string|min:5|max:50|email|unique:users',
            'password' => 'required|string|confirmed|min:6|max:8',
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();
            return response()->json([
                'success' => false,
                'error' => $errors,
            ], 401);
        }


        if ($validation->passes()) {

            // Mettre à jour la colonne 'photo' dans la table des utilisateurs avec le chemin du fichier
            $user = User::create([
                "firstname" => $request->firstname,
                "lastname" => $request->lastname,
                "email" => $request->email,
                "gender" => $request->gender,
                "phone" => $request->phone,
                "email" => $request->email,
                'password' => Hash::make($request->password),
                'role_id' => $request->role_id
            ]);

            return response()->json([
                'message' => 'User created sucessfuly',
                "data" => $user,
            ], 200);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/user/getAllUser",
     *     tags={"user"},
     *     summary="Get all Users",
     *     description="Retrieve a list of all users",
     *     @OA\Response(
     *         response=200,
     *         description="List of users",
     *     ),
     * )
     */
    // get all user
    public function getAllUser()
    {
        // $users = User::orderBy('firstname', 'asc')->paginate(20);
        $users = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.email',
                'users.phone',
                'users.gender',
                'roles.name as role_id',
                'users.status',
                'users.profile_image',
                'users.email_verified_at',
                'users.deleted_at',
                'users.created_at',
                'users.updated_at',
            )
            ->orderBy('firstname', 'asc')->paginate(20);

        return response()->json([
            'data' => $users
        ], 200);
    }

    public function getRole(Request $request)
    {
        $role = Role::all();
        return response()->json([
            'Roles' => $role
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/showUser/{user}",
     *     tags={"user"},
     *     summary="Get user by ID",
     *     description="Get user details by user ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *     ),
     * )
     */
    // Sho single user detail
    public function showUser($id)
    {
        $user = DB::table('users')
            ->join('roles', 'users.role_id', '=', 'roles.id')
            ->select(
                'users.id',
                'users.firstname',
                'users.lastname',
                'users.email',
                'users.phone',
                'users.gender',
                'roles.name as role_id',
                'users.status',
                'users.profile_image',
                'users.email_verified_at',
                'users.deleted_at',
                'users.created_at',
                'users.updated_at',
            )
            ->where('users.id', $id)
            ->get();

        return response()->json([
            'data' => $user
        ], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/user/updateUser/{user}",
     *     tags={"user"},
     *     summary="Update User",
     *     description="Update details of a user by their ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the user",
     *         @OA\Schema(type="integer"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="User data to be updated",
     *         @OA\JsonContent(
     *             type="object",
     *             required={"firstname", "email", "phone", "gender" , "role_id"},
     *             properties={
     *                 @OA\Property(property="firstname", type="string"),
     *                 @OA\Property(property="lastname", type="string"),
     *                 @OA\Property(property="email", type="email"),
     *                 @OA\Property(property="phone", type="number"),
     *                 @OA\Property(property="gender", type="string"),
     *                 @OA\Property(property="role_id", type="string"),
     *             },
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User successful updated.",
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *     ),
     * )
     */
    // Update single user
    public function updateUser(Request $request, $user_Id)
    {
        $validation = FacadesValidator::make($request->all(), [
            'firstname' => 'required|string|max:250',
            'lastname' => 'string|max:250',
            'gender' => 'string|max:10',
            'phone'  => 'integer|min:9|',
            'email' => 'required|string|max:50',
            'role_id' => 'required|exists:roles,id'
        ]);

        if ($validation->fails()) {
            return response()->json([
                'error' => $validation->errors(),
                'status' => 422
            ], 422);
        }

        // Retrieve the user from the database
        $user = User::find($user_Id);
        $userEmail = $request->email;
        $userPhone = $request->phone;
        $existingUserEmail = User::where('email', $userEmail)
            ->where('id', '!=', $user_Id) // Exclure l'utilisateur en cours de modification
            ->first();
        $existingUserPhone = User::where('phone', $userEmail)
            ->where('id', '!=', $user_Id) // Exclure l'utilisateur en cours de modification
            ->first();

        if ($existingUserEmail) {
            return response()->json([
                'error' => 'This email has already been taking'
            ], 423);
        }

        if ($existingUserPhone) {
            return response()->json([
                'error' => 'This phone has already been taking'
            ], 424);
        }

        if (!$user) {
            return response()->json([
                'error' => 'User not found'
            ], 404);
        }

        // if ($request->user()->hasRole('admin')) {
        // if (auth()->user()->role->name === 'admin') {
        // Update the user's properties with the new data
        $user->firstname = $request->firstname;
        $user->lastname = $request->lastname;
        $user->gender = $request->gender;
        $user->phone = $request->phone;
        $user->email = $request->email;
        $user->role_id = $request->role_id;

        // Save the changes to the database
        $user->update();
        // } else {
        //     return response()->json([
        //         "msg" => "No",
        //     ] , 406);
        // }


        return response()->json([
            "msg" => "User successful updated.",
        ]);
    }

    /**
     * @OA\Post(
     *     path="/api/user/toggleStatus/{user}",
     *     tags={"user"},
     *     summary="Activate/Deactivate user",
     *     description="Activate or deactivate a user account by user ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Id of the user",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User account status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Cet utilisateur a bien été activé"
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="User account status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Cet utilisateur a bien été désactivé"
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *     ),
     * )
     */
    // Change user status
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        // Changer le statut de l'utilisateur
        $newStatus = !$user->status;
        $user->update(['status' => $newStatus]);

        // Message à retourner en fonction du nouveau statut
        $message = $newStatus ? 'Cet utilisateur a été activé avec succes.' : 'Cet utilisateur a été désactivé avec succes.';

        return response()->json(['message' => $message], 200);
    }

    /**
     * @OA\Put(
     *     path="/api/user/upload_profileImage",
     *     tags={"user"},
     *     summary="Update user profile image",
     *     description="Update the profile image",
     *     @OA\RequestBody(
     *         required=true,
     *         description="User profile image details",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"profile_image"},
     *                 @OA\Property(
     *                     property="profile_image",
     *                     type="string",
     *                     format="file",
     *                     description="User profile image file"
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="User profile image updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Photo de profil mise à jour avec succès"
     *             ),
     *             @OA\Property(
     *                 property="user",
     *                 type="object",
     *                 ref="#/components/schemas/User"
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="User not found",
     *     ),
     * )
     */

    // update user profile_image
    public function upload_photo1(Request $request)
    {
        // validation
        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif,JPEG,PNG,JPG,GIF,jfif,JFIF|max:8096',
        ]);

        // if ($validation->fails()) {
        //     $errors = $validation->errors();
        //     return response()->json([
        //         "errors" => $errors,
        //         "status" => 401,
        //     ]);
        // }

        // if ($validation->passes()) {
        // file name
        $filename = time() . '.' . $request->file('profile_image')->getClientOriginalExtension();

        // Move the uploaded file to the desired storage location
        $path = $request->file('profile_image')->storeAs('images/users', $filename, 'public');

        // Update the 'profile_image' column in the users table with the file path
        $user = User::findOrFail($request->user_id);
        $user->profile_image = $path;
        $user->save();

        return response()->json([
            'message' => 'Profile image updated successfully',
            'data' => $user,
        ]);
        // }
    }

    public function upload_photo(Request $request)
    {
        $request->validate([
            'profile_image' => 'required|mimes:png,jpg,jpeg,PNG,JPG,JPEG,jfif,JFIF|max:4000'
        ]);

        $filename = time() . '.' . $request->profile_image->extension();
        $path = $request->profile_image->storeAs('images/users', $filename, 'public');

        $request->user()->update([
            'profile_image' => $path
        ]);

        return response([
            'message' => 'Your profile picture has been successfully updated!',
            'path'    => $path,
            'profile_image' => $request->user()->profile_image
        ], 201);
    }
}
