<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Imports\PlaceImport;
use App\Imports\TableImport;
use App\Models\Category;
use App\Models\Event;
use App\Models\Place;
use App\Models\PlaceTable;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TableController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/table/addPlace",
     *     tags={"table"},
     *     summary="Add place",
     *     description="Create a new place",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Place detail",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", description="Place's name"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Place added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Place added successfully"),
     *             @OA\Property(property="place", ref="#/components/schemas/Place"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}}),
     *         ),
     *     ),
     * )
     *
     * @OA\Schema(
     *     schema="Place",
     *     required={"name", "description"},
     *     @OA\Property(property="name", type="string", description="Event type name"),
     * )
     */
    // Add place
    public function addPlace(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json([
                "errors" => $errors,
                "status" => 401,
            ]);
        }

        if ($validation->passes()) {
            $place = Place::create([
                "name" => $request->name,
            ]);

            return response()->json([
                'message' => 'Place created sucessfuly',
                $place, 201,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/table/addCategory",
     *     tags={"table"},
     *     summary="Add category of place",
     *     description="Create a new category of place",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Category detail",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", description="Category's name"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Category added successfully"),
     *             @OA\Property(property="category", ref="#/components/schemas/Category"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}}),
     *         ),
     *     ),
     * )
     *
     * @OA\Schema(
     *     schema="Category",
     *     required={"name", "description"},
     *     @OA\Property(property="name", type="string", description="Category place name"),
     * )
     */
    // Add category
    public function addCategory(Request $request)
    {

        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json([
                "errors" => $errors,
                "status" => 401,
            ]);
        }

        if ($validation->passes()) {
            $category = Category::create([
                "name" => $request->name,
            ]);

            return response()->json([
                'message' => 'Category created sucessfuly',
                $category, 201,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/table/addTable",
     *     tags={"table"},
     *     summary="Add new table",
     *     description="Create a new table",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Table details",
     *         @OA\JsonContent(
     *             required={"name", "categorie_id","capacity", "event_id"},
     *             @OA\Property(property="name", type="string", description="Table's name"),
     *             @OA\Property(property="capacity", type="number", description="Table's capacity"),
     *             @OA\Property(property="categorie_id", type="string", description="Table's categorie_id"),
     *             @OA\Property(property="event_id", type="string", description="Table's event_id"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Table added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Table added successfully"),
     *             @OA\Property(property="table", ref="#/components/schemas/Table")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={
     *                  "name": {"The name field is required."},
     *                  "capacity": {"The capacity field is required."},
     *                  "categorie_id": {"The categorie_id field is required."},
     *                  "event_id": {"The event_id field is required."}
     *              }),
     *         ),
     *     ),
     * )
     *
     * @OA\Schema(
     *      schema = "Table",
     *      required={"name", "categorie_id","capacity", "event_id"},
     *      @OA\Property(property="name", type="string", description="Table's name"),
     *      @OA\Property(property="capacity", type="number", description="Table's capacity"),
     *      @OA\Property(property="status", type="boolean", description="Table's status"),
     *      @OA\Property(property="guests", type="string", description="Table's guests"),
     *      @OA\Property(property="rest_of_place", type="number", description="Table's rest_of_place"),
     *      @OA\Property(property="qr_code_path", type="string", description="Table's qr_code_path"),
     *      @OA\Property(property="categorie_id", type="string", description="Table's categorie_id"),
     *      @OA\Property(property="event_id", type="string", description="Table's event_id"),
     * )
     */

    // Add table
    public function addTable(Request $request)
    {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            'capacity' => 'required',
            'categorie_id' => 'required|string|exists:categories,id',
            'event_id' => 'required|string|exists:events,id',
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json([
                "errors" => $errors,
                "status" => 401,
            ]);
        }

        if ($validation->passes()) {
            $table = Table::create([
                "name" => $request->name,
                "capacity" => $request->capacity,
                "rest_of_place" => $request->capacity,
                "categorie_id" => $request->categorie_id,
                "event_id" => $request->event_id,
            ]);

            if ($table) {
                $event = Event::where('id', $table->event_id)->first();

                $rest_of_place  =  $event->number_of_space - $table->capacity;

                if ($rest_of_place < 0) {
                    return response()->json([
                        'place disponible',
                        $event->rest_of_space,
                    ], 406);
                }

                $event->rest_of_place = $rest_of_place;
                $event->save();
            }


            // // Générer le code QR
            // if ($tableInfo) {
            //     $qrCode = QrCode::generate("$tableInfo->name - $tableInfo->capacity - $tableInfo->categorie_name - $tableInfo->event_name");
            // }
            // $qrCode = QrCode::generate("$table->name - $table->capacity - $table->categorie_name - $table->event_name");


            // // Enregistrer le code QR dans le dossier public/qrcodes
            // $qrCodePath = 'qrcodes/' . time() . '_' . $table->name . '.png';
            // Storage::disk('public')->put($qrCodePath, $qrCode);
            // $table1 = Table::create([
            //     "qr_code_path" => $qrCodePath,
            // ]);



            return response()->json([
                'Table created sucessfuly',
                $rest_of_place,
                $table,
            ], 200);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/table/attributeTable/{attributeTable}",
     *     tags={"table"},
     *     summary="Assign a place to a table",
     *     description="Assign a place",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the table",
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *        description="Assign detail",
     *         @OA\JsonContent(
     *             required={"place_id"},
     *             @OA\Property(property="place_id", type="string", description="Assign's place_id"),
     *         ),
     *     ),
     *    @OA\Response(
     *         response=201,
     *         description="Place successfully assigned to the table",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Place successfully assigned to the table"),
     *             @OA\Property(property="placeTable", ref="#/components/schemas/PlaceTable"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={"
     *                  place_id": {"The place_id field is required.", "This place has already been assigned to a guest, try another"}
     *              }),
     *         ),
     *     ),
     * )
     *
     * @OA\Schema(
     *     schema="PlaceTable",
     *     required={"place_id"},
     *     @OA\Property(property="place_id", type="string", description="Assign's place_id"),
     * )
     */

    // assign a place to a table
    public function attributePlace(Request  $request, $id)
    {
        $restPlace = Table::select('rest_of_place')->where('id', $id)->first();

        if ($restPlace) {
            if ($restPlace->rest_of_place === 0) {
                return response()->json([
                    'msg' => 'This table is already occupied',
                    "status" => 201
                ]);
            } else {
                // validation
                $validation = Validator::make($request->all(), [
                    "place_id"  => 'required|exists:places,id|unique:place_tables,place_id',
                ]);


                // $Place_id = $request->place_id;
                // $existingPlace = PlaceTable::where('place_id', $Place_id)
                //     ->where('table_id', $id)
                //     ->get();

                // if ($existingPlace) {
                //     return response()->json([
                //         "msg" => "This place have already been takeng , please try another one.",
                //          408,
                //     ]);
                // }

                if ($validation->fails()) {
                    $errors = $validation->errors();

                    return response()->json([
                        "errors" => $errors,
                        "status" => 401
                    ]);
                }

                if ($validation->passes()) {

                    // if (Table::where('id', $id)->exists()) {
                    //     // Si la condition est vraie, effectuez l'insertion
                    //     Table::where('id', $id)->update(['place_id' => $request->place_id]);
                    // }
                    // $place = Table::create([
                    //     "place_id" => $request->place_id,
                    // ]);

                    $placeTable = PlaceTable::create([
                        "table_id" => $id,
                        "place_id" => $request->place_id,
                    ]);

                    if ($placeTable) {
                        // I decrement the value of the rest of place by 1 in the table table
                        DB::table('tables')->where('id', $id)->decrement('rest_of_place');
                    }

                    //  update  the value of status to 0 if value of rest_of_place is 0
                    Table::where('rest_of_place', 0)
                        ->where('id',  $id)
                        ->update(['status' => '0']);

                    // mettre ajour le nombtre de table restant dans la table event
                    // $tableId = Table::select('id')->where('id', $id)->first();
                    // $tableId = Table::where('id', $id);
                    // if ($tableId) {
                    //     if($restPlace->rest_of_place === 0){
                    //       DB::table('events')->where('id', $tableId->id)->decrement('rest_of_table');
                    //     }

                    // }

                    return response()->json([
                        'msg' => 'Successfully assigned place at the table',
                        "status" => 200
                    ]);
                }
            }
        }
    }

    /**
     * @OA\Put(
     *     path="/api/table/updateTable/{id}",
     *     tags={"table"},
     *     summary="Update a single table",
     *     description="Update details of an existing table",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the table to update",
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated table details",
     *         @OA\JsonContent(
     *             required={"name", "categorie_id", "capacity", "event_id"},
     *             @OA\Property(property="name", type="string", description="Updated table name"),
     *             @OA\Property(property="capacity", type="number", description="Updated table capacity"),
     *             @OA\Property(property="categorie_id", type="string", description="Updated table categorie_id"),
     *             @OA\Property(property="event_id", type="string", description="Updated table event_id"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Table updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Table updated successfully"),
     *             @OA\Property(property="table", ref="#/components/schemas/Table"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Table not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={
     *                 "name": {"The name field is required."},
     *                 "capacity": {"The capacity field is required."},
     *                 "categorie_id": {"The categorie_id field is required."},
     *                 "event_id": {"The event_id field is required."}
     *             }),
     *         ),
     *     ),
     * )
     */
    // Update single table
    public function updateTable(Request $request, $table_Id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:50',
            // 'capacity' => 'required',
            'categorie_id' => 'string|exists:categories,id',
            'event_id' => 'string|exists:events,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors(), 'status' => 421],);
        }

        // Retrieve the user from the database
        $table = Table::find($table_Id);

        if (!$table) {
            return response()->json(['error' => 'Table not found'], 404);
        }

        // Update the table's properties with the new data
        $table->name = $request->name;
        // $table->capacity = $request->capacity;
        $table->categorie_id = $request->categorie_id;
        $table->event_id = $request->event_id;

        // Save the changes to the database
        $table->update();
        return response()->json([
            "msg" => "Table successful updated.",
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/table/getAllTable",
     *     tags={"table"},
     *     summary="Get all table",
     *     description="Retrieve a list of all table",
     *     @OA\Response(
     *         response=200,
     *         description="List of all table",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Table"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="No table found",
     *     ),
     * )
     */

    // get all table
    public function getAllTable()
    {

        // $event = Table::orderBy('name', 'asc')->paginate(20);
        $table = DB::table('tables')
            ->join('categories', 'tables.categorie_id', '=', 'categories.id')
            ->join('events', 'tables.event_id', '=', 'events.id')
            ->select(
                'tables.id',
                'tables.name',
                'tables.status',
                'tables.capacity',
                'tables.rest_of_place',
                'categories.name as categorie_name',
                'events.name as event_name',
            )
            ->orderBy('tables.name', 'asc')
            ->paginate(20);
        return response()->json([
            'data' => $table,
            'status' => 200
        ]);
    }

    public function getAllCategory()
    {
        $category = Category::orderBy('name', 'asc')->get();
        return response()->json([
            'data' => $category
        ], 200);
    }

    public function getAllPlace()
    {
        $place = Place::all();
        return response()->json([
            'data' => $place
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/table/showTable/{table}",
     *     tags={"table"},
     *     summary="Get table by ID",
     *     description="Retrieve details of a specific table by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the table",
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="table details",
     *         @OA\JsonContent(ref="#/components/schemas/Table"),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Table not found",
     *     ),
     * )
     */
    // Sho single table detail
    public function showTable($id)
    {
        $table = DB::table('tables')
            ->join('categories', 'tables.categorie_id', '=', 'categories.id')
            ->join('events', 'tables.event_id', '=', 'events.id')
            ->select(
                'tables.id',
                'tables.name',
                'tables.status',
                'tables.capacity',
                'tables.rest_of_place',
                'categories.name as categorie_name',
                'events.name as event_name',

            )
            ->where('tables.id', $id)
            ->get();

        $guests = DB::table('guests')
            ->join('tables', 'guests.table_id', '=', 'tables.id')
            ->select(
                'guests.firstname',
                'guests.lastname',
            )
            ->where('tables.id', $id)
            ->get();

        $tablePlace = DB::table('place_tables')
            ->join('places', 'place_tables.place_id', '=', 'places.id')
            ->join('tables', 'place_tables.table_id', '=', 'tables.id')
            ->select(
                'places.name as place_name',
            )
            ->where('tables.id', $id)
            ->get();


        return response()->json([
            'data' => $table,
            'place' => $tablePlace,
            'guest' => $guests,
            'status' => 200
        ]);
    }

    //     public function showTable($id)
    // {
    //     $tableData = DB::table('tables')
    //         ->select([
    //             'tables.id',
    //             'tables.name',
    //             'tables.status',
    //             'tables.capacity',
    //             'tables.rest_of_place',
    //             DB::raw('categories.name AS categorie_name'),
    //             DB::raw('events.name AS event_name'),
    //             'guests.firstname',
    //             'guests.lastname',
    //             DB::raw('places.name AS place_name'),
    //         ])
    //         ->leftJoin('categories', 'tables.categorie_id', '=', 'categories.id')
    //         ->leftJoin('events', 'tables.event_id', '=', 'events.id')
    //         ->leftJoin('guests', 'guests.table_id', '=', 'tables.id')
    //         ->leftJoin('place_tables', 'place_tables.table_id', '=', 'tables.id')
    //         ->leftJoin('places', 'place_tables.place_id', '=', 'places.id')
    //         ->where('tables.id', $id)
    //         ->first();

    //     if (!$tableData) {
    //         return response()->json(['message' => 'Table not found', 'status' => 404]);
    //     }

    //     return response()->json([
    //         'data' => $tableData,
    //         'status' => 200,
    //     ]);
    // }

    // Validate the Excel file, if necessary
    // $request->validator([
    //     'file' => 'required|mimes:xlsx,xls',
    // ]);

    public function importView(Request $request)
    {
        return view('importFile');
    }

    /**
     * @OA\Post(
     *     path="/api/table/import/place",
     *     tags={"table"},
     *     summary="Import place from CSV",
     *     description="Import place from a CSV file.",
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
     *                     description="CSV file containing place data"
     *                 ),
     *             ),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Place imported successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Place imported successfully"),
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

    public function importPlace(Request $request)
    {

        $path =  $request->file('file')->store('files');
        Excel::import(new PlaceImport, $path);
        return response(['msg' => "Your places list has been added successfully !"]);
    }

    public function exportPlace(Request $request)
    {
        return Excel::download(new PlaceImport, 'places.xlsx');
    }
}
