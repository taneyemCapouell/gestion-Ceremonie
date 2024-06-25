<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\GuestImport;
use App\Models\Guest;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class GuestController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/guest/addGuest",
     *     tags={"guest"},
     *     summary="Add new guest",
     *     description="Create a new guest",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Guest details",
     *         @OA\JsonContent(
     *             required={"firstname", "email", "phone", "place_id", "table_id", "event_id"},
     *             @OA\Property(property="firstname", type="string", description="Guest's firstname"),
     *             @OA\Property(property="lastname", type="string", description="Guest's lastname"),
     *             @OA\Property(property="email", type="string", description="Guest's email"),
     *             @OA\Property(property="gender", type="string", description="Guest's gender"),
     *             @OA\Property(property="phone", type="string", description="Guest's phone"),
     *             @OA\Property(property="place_id", type="string", description="Guest's place_id"),
     *             @OA\Property(property="table_id", type="string", description="Guest's table_id"),
     *             @OA\Property(property="event_id", type="string", description="Guest's event_id"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Guest added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Guest added successfully"),
     *             @OA\Property(property="guest", ref="#/components/schemas/Guest"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={
     *                  "firstname": {"The firstname field is required."},
     *                  "email": {"The email field is required.", "The email field must be in the form of an email address."},
     *                  "phone": {"The phone field is required."},
     *                  "place_id": {"The place_id field is required."},
     *                  "table_id": {"The table_id field is required."},
     *                  "event_id": {"The event_id field is required."},
     *             }),
     *         ),
     *     ),
     * )
     *
     * @OA\Schema(
     *     schema="Guest",
     *     required={"firstname", "email", "phone", "place_id", "table_id", "event_id"},
     *     @OA\Property(property="firstname", type="string", description="Guest's firstname"),
     *     @OA\Property(property="lastname", type="string", description="Guest's lastname"),
     *     @OA\Property(property="email", type="string", description="Guest's email"),
     *     @OA\Property(property="gender", type="string", description="Guest's gender"),
     *     @OA\Property(property="phone", type="string", description="Guest's phone"),
     *     @OA\Property(property="status", type="enum", description="Guest's status"),
     *     @OA\Property(property="place_id", type="string", description="Guest's place_id"),
     *     @OA\Property(property="table_id", type="string", description="Guest's table_id"),
     *     @OA\Property(property="event_id", type="string", description="Guest's event_id"),
     * )
     */

    // Add guest
    public function addGuest(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'firstname' => 'required|string|max:30',
            'lastname' => 'string|max:30',
            'email' => 'required|string|max:50|email|unique:guests',
            'gender' => 'required|string|max:20',
            'phone' => 'required|string||unique:guests',
            'place_id'  => 'string|exists:places,id',
            'table_id' => 'string|exists:tables,id',
            'event_id' => 'string|exists:events,id',
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json([
                "errors" => $errors,
                "status" => 401,
            ]);
        }

        // Get the  event id in session
        // if ($request->session()->has('event_id')) {
        //     $event_id = Session::get('event_id');
        // }

        if ($validation->passes()) {
            $guest = Guest::create([
                "firstname" => $request->firstname,
                "lastname" => $request->lastname,
                "email" => $request->email,
                "gender" => $request->gender,
                "phone" => $request->phone,
                "place_id" => $request->place_id,
                "table_id" => $request->table_id,
                "event_id" =>  $request->event_id
            ]);

            // update the rest of place in event
            // DB::table('events')->update(
            //     ['id' => $event],
            //     ['rest_of_space' => DB::raw('rest_of_space - ' . $capacityValue)]
            // );
            // I decrement the value of the rest of place by 1 in the event table
            DB::table('events')->where('id', $guest->event_id)->decrement('rest_of_space');

            return response()->json([
                'msg' => 'Guest created sucessfuly',
                'data' => $guest,
                201
            ]);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/guest/updateGuest/{guest}",
     *     tags={"guest"},
     *     summary="Update an guest",
     *     description="Update an existing guest by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Id of the guest",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Guest details",
     *         @OA\JsonContent(
     *             required={"firstname", "email", "phone"},
     *             @OA\Property(property="firstname", type="string", description="Guest's firstname"),
     *             @OA\Property(property="lastname", type="string", description="Guest's lastname"),
     *             @OA\Property(property="email", type="string", description="Guest's email"),
     *             @OA\Property(property="phone", type="string", description="Guest's phone"),
     *             @OA\Property(property="place_id", type="string", description="Guest's place_id"),
     *             @OA\Property(property="table_id", type="string", description="Guest's table_id"),
     *             @OA\Property(property="event_id", type="string", description="Guest's event_id"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Guest updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Guest updated successfully"),
     *             @OA\Property(property="guest", ref="#/components/schemas/Guest"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guest not found",
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
     *                    "firstname": {"The firstname field is required."},
     *                      "email": {"The email field is required.", "The email field must be in the form of an email address."},
     *                      "phone": {"The phone field is required."},
     *             }),
     *             ),
     *         ),
     *     ),
     * )
     *
     */
    // Update single guest
    public function updateGuest(Request $request, $guest_Id)
    {
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|string|max:50',
            'lastname' => 'required|string|max:50',
            'phone' => 'required|string|max:15',
            'place_id'  => 'string|exists:places,id',
            'table_id' => 'string|exists:tables,id',
            'event_id' => 'string|exists:events,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => 422],);
        }

        // Retrieve the guest id from the database
        $guest = Guest::find($guest_Id);

        if (!$guest) {
            return response()->json(['error' => 'Guest not found'], 404);
        }

        // Update the guest's properties with the new data
        $guest->firstname = $request->firstname;
        $guest->lastname = $request->lastname;
        $guest->phone = $request->phone;
        $guest->place_id = $request->place_id;
        $guest->table_id = $request->table_id;
        $guest->event_id = $request->event_id;

        // Save the changes to the database
        $guest->update();
        return response()->json([
            "msg" => "Guest successful updated.",
        ]);
    }

    // Update  guest status
    public function updateStatus(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => 422]);
        }


        // Update the guest's properties with the new data
        $guest = Guest::findOrFail($id);
        $guest->status = $request->status;
        $guest->save();


        // Save the changes to the database
        $guest->update();
        return response()->json([
            "msg" => "Guest status successful updated.",
            "status" => $guest->status,
        ]);
    }


    /**
     * @OA\Get(
     *     path="/api/guest/getAllGuest",
     *     tags={"guest"},
     *     summary="Get all guest",
     *     description="Retrieve a list of all guest",
     *     @OA\Response(
     *         response=200,
     *         description="List of all guest",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Guest"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No owners found",
     *     ),
     * )
     */
    // get all guest
    public function getAllGuest()
    {
        $guest = DB::table('guests')
            ->join('places', 'guests.place_id', '=', 'places.id')
            ->join('tables', 'guests.table_id', '=', 'tables.id')
            ->join('events', 'guests.event_id', '=', 'events.id')
            ->select(
                'guests.id',
                'guests.firstname',
                'guests.lastname',
                'guests.email',
                'guests.gender',
                'guests.status',
                'guests.phone',
                'places.name  as place_name',
                'tables.name as table_name',
                'events.name as event_name'
            )
            ->orderBy('guests.firstname', 'asc')
            ->paginate(20);

        return response()->json([
            'data' => $guest,
            'status' => 200
        ]);
    }

    // get all table present in the event
    public function getAllTableInEvent($id)
    {
        $tableInEvent = Table::where('event_id', $id)->get();

        return response()->json([
             $tableInEvent,
             200
        ]);
    }

    // get all place present in the table
    public function getAllPlaceInTable($id)
    {
        $placeInTable = DB::table('place_tables')
            ->join('places', 'place_tables.place_id', '=', 'places.id')
            ->join('tables', 'place_tables.table_id', '=', 'tables.id')
            ->select(
                'places.id',
                'places.name  as place_name',
            )
            ->orderBy('places.name', 'asc')
            ->where('place_tables.table_id', $id)
            ->get();

        if ($placeInTable) {
            return response()->json([
                'msg' => $placeInTable,
                200
            ]);
        } else {
            return response()->json([
                'msg' => "tables not found",
                400
            ]);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/guest/showGuest/{guest}",
     *     tags={"guest"},
     *     summary="Show guest detail by ID",
     *     description="Retrieve details of a specific guest by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the guest",
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Guest details",
     *         @OA\JsonContent(ref="#/components/schemas/Guest"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guest not found",
     *     ),
     * )
     */
    // Sho single guest detail
    public function showGuest($id)
    {
        $guest = DB::table('guests')
            ->join('places', 'guests.place_id', '=', 'places.id')
            ->join('tables', 'guests.table_id', '=', 'tables.id')
            ->join('events', 'guests.event_id', '=', 'events.id')
            ->select(
                'guests.id',
                'guests.firstname',
                'guests.lastname',
                'guests.email',
                'guests.gender',
                'guests.status',
                'guests.phone',
                'places.name as place_name',
                'tables.name as table_name',
                'events.name as event_name'
            )
            ->where('guests.id', $id)
            ->first();

        if ($guest) {
            return response()->json([
                'data' => $guest,
                'status' => 200
            ]);
        } else {
            return response()->json([
                'message' => 'Guest not found',
                'status' => 404
            ], 404);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/guest/import/guest",
     *     tags={"guest"},
     *     summary="Import guest from CSV",
     *     description="Import guest from a CSV file.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="CSV file",
     *         @OA\MediaType(
     *             mediaType="multipart/form-data",
     *             @OA\Schema(
     *                 required={"file"},
     *                 @OA\Property(
     *                     property="file",
     *                     type="file",
     *                     format="binary",
     *                     description="CSV file containing guest data"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Guest imported successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Guest imported successfully"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error or import failure",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error or import failure"),
     *             @OA\Property(property="errors", type="object", example={"file": {"The file field is required."}}),
     *         ),
     *     ),
     * )
     */
    // import guests list
    public function importGuest(Request $request)
    {
        if ($request->hasFile('file')) {
            $path =  $request->file('file')->store('files');
            Excel::import(new GuestImport, $path);
            return response(['msg' => "Your guest's list has been added successfully"]);
        } else {
            return response()->json(['msg' => 'Aucun fichier trouvé'], 400);
        }
    }

    // export guests list
    public function exportTable(Request $request)
    {
        return Excel::download(new GuestImport, 'guests.xlsx');
    }
}
