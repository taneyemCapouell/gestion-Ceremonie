<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class OwnerController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/owner/addOwner",
     *     tags={"owner"},
     *     summary="Add owner",
     *     description="Create a new owner",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Owner details",
     *         @OA\JsonContent(
     *             required={"firstname", "email", "phone"},
     *             @OA\Property(property="firstname", type="string", description="Owner's firstname"),
     *             @OA\Property(property="lastname", type="string", description="Owner's lastname"),
     *             @OA\Property(property="email", type="string", description="Owner's email"),
     *             @OA\Property(property="gender", type="string", description="Owner's gender"),
     *             @OA\Property(property="adresse", type="string", description="Owner's adresse"),
     *             @OA\Property(property="phone", type="string", description="Owner's phone"),
     *             @OA\Property(property="description", type="string", description="Owner's description"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Owner added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Owner added successfully"),
     *             @OA\Property(property="owner", type="object", ref="#/components/schemas/Owner")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={
     *                  "firstname": {"The firstname field is required."},
     *                  "email": {"The email field is required.", "The email field must be in the form of an email address." , "there is already a user with this email address, please try another"},
     *                  "phone": {"The phone field is required."}
     *              }),
     *         ),
     *     ),
     * )
     *
     * @OA\Schema(
     *     schema="Owner",
     *     required={"firstname", "email", "phone"},
     *     @OA\Property(property="firstname", type="string", description="Owner's firstname"),
     *     @OA\Property(property="lastname", type="string", description="Owner's lastname"),
     *     @OA\Property(property="email", type="string", description="Owner's email"),
     *     @OA\Property(property="gender", type="string", description="Owner's gender"),
     *     @OA\Property(property="adresse", type="string", description="Owner's adresse"),
     *     @OA\Property(property="phone", type="string", description="Owner's phone"),
     *     @OA\Property(property="description", type="string", description="Owner's description"),
     * )
     */
    // Add uowner
    public function addOwner(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'string|max:50',
            'email' => 'required|string|max:50|email|unique:owners',
            'description'  => 'string',
            'gender' => 'string|max:9',
            'adresse' => 'string|min:2|max:50',
            'phone'  => 'required|integer|min:9|unique:owners',

        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json([
                "error" => $errors,
            ] ,401 );
        }

        if ($validation->passes()) {
            $owner = Owner::create([
                "firstname" => $request->firstname,
                "lastname" => $request->lastname,
                "email" => $request->email,
                "gender" => $request->gender,
                "adresse" => $request->adresse,
                "phone" => $request->phone,
                "description" => $request->description,
            ]);

            return response()->json([
                'message' => 'Owner created sucessfuly',
                'owner' => $owner,
            ] , 200);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/owner/updateOwner/{owner}",
     *     tags={"owner"},
     *     summary="Update an Owner",
     *     description="Update an existing owner by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Id of the owner",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Owner details",
     *         @OA\JsonContent(
     *             required={"firstname", "email", "phone"},
     *             @OA\Property(property="firstname", type="string", description="Owner's firstname"),
     *             @OA\Property(property="lastname", type="string", description="Owner's lastname"),
     *             @OA\Property(property="email", type="string", description="Owner's email"),
     *             @OA\Property(property="gender", type="string", description="Owner's gender"),
     *             @OA\Property(property="adresse", type="string", description="Owner's adresse"),
     *             @OA\Property(property="phone", type="string", description="Owner's phone"),
     *             @OA\Property(property="description", type="string", description="Owner's description"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Owner updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Owner updated successfully"),
     *             @OA\Property(property="owner", ref="#/components/schemas/Owner"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Owner not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object",
     *                 example={
     *                     "firstname": {"The firstname field is required."},
     *                     "email": {"The email field is required.", "The email field must be in the form of an email address."},
     *                     "phone": {"The phone field is required."},
     *                 }
     *             ),
     *         ),
     *     ),
     * )
     *
     */

    // Update single owner
    public function updateOwner(Request $request, $owner_Id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            // 'email' => 'required|string|max:50|email|',
            'description'  => 'string',
            'gender' => 'string',
            'adresse' => 'string|max:50',
            'phone'  => 'required|integer',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => 422],);
        }

        // Retrieve the user from the database
        $owner = Owner::find($owner_Id);

        if (!$owner) {
            return response()->json(['error' => 'Owner not found'], 404);
        }

        // Update the owner's properties with the new data
        $owner->firstname = $request->firstname;
        $owner->lastname = $request->lastname;
        $owner->gender = $request->gender;
        $owner->phone = $request->phone;
        // $owner->email = $request->email;
        $owner->adresse = $request->adresse;
        $owner->description = $request->description;

        // Save the changes to the database
        $owner->update();
        return response()->json([
            "msg" => "Owner successful updated.",
        ]);
    }

    /**
     * @OA\Get(
     *     path="/api/owner/getAllOwner",
     *     tags={"owner"},
     *     summary="Get all Owners",
     *     description="Retrieve a list of all owners",
     *     @OA\Response(
     *         response=200,
     *         description="List of all owners",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Owner"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No owners found",
     *     ),
     * )
     */

    // get all owner's
    public function getAllOwner()
    {
        $owners = Owner::orderBy('firstname', 'asc')->paginate(20);
        return response()->json($owners);
    }

    /**
     * @OA\Get(
     *     path="/api/owner/showOwner/{owner}",
     *     tags={"owner"},
     *     summary="Show Owner detail by ID",
     *     description="Retrieve details of a specific owner by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the owner",
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Owner details",
     *         @OA\JsonContent(ref="#/components/schemas/Owner"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Owner not found",
     *     ),
     * )
     */
    // Sho single owner detail
    public function showOwner($id)
    {
        $owner = Owner::findOrFail($id);

        return response()->json([
            'data' => $owner
        ], 200);
    }
}
