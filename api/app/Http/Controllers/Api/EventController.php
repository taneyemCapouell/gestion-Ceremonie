<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\Event;
use App\Models\Event_type;
use App\Models\Gerer;
use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class EventController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/event/addEventType",
     *     tags={"event"},
     *     summary="Add Event Type",
     *     description="Create a new event type",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Event type details",
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string", description="Event type name"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Event type added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Event type added successfully"),
     *             @OA\Property(property="event_type", ref="#/components/schemas/EventType"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Erreur de validation"),
     *             @OA\Property(property="errors", type="object", example={"name": {"The name field is required."}}),
     *         ),
     *     ),
     * )
     *
     * @OA\Schema(
     *     schema="EventType",
     *     required={"name", "description"},
     *     @OA\Property(property="name", type="string", description="Place's name"),
     * )
     */

    // Add event type
    public function addEventType(Request $request)
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
            $event_type = Event_type::create([
                "name" => $request->name,
            ]);

            return response()->json([
                'message' => 'Event type created sucessfuly',
                $event_type, 201,
            ]);
        }
    }

    /**
     * @OA\Post(
     *     path="/api/event/addEvent",
     *     tags={"event"},
     *     summary="Add new event",
     *     description="Create a new event",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Event details",
     *         @OA\JsonContent(
     *              required={"name", "location","city","date_start","time", "owner_id","number_of_space","number_of_table", "event_type_id"},
     *             @OA\Property(property="name", type="string", description="Event's name"),
     *             @OA\Property(property="location", type="string", description="Event's location"),
     *             @OA\Property(property="description", type="string", description="Event's description"),
     *             @OA\Property(property="city", type="string", description="Event's city"),
     *             @OA\Property(property="date_start", type="string", description="Event's date_start"),
     *             @OA\Property(property="time", type="string", description="Event's time"),
     *             @OA\Property(property="neighborhood", type="string", description="Event's neighborhood"),
     *             @OA\Property(property="owner_id", type="string", description="Event's owner_id"),
     *             @OA\Property(property="number_of_space", type="string", description="Event's number_of_space"),
     *             @OA\Property(property="number_of_table", type="string", description="Event's number_of_table"),
     *             @OA\Property(property="event_type_id", type="string", description="Event's event_type_id"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Event added successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Event added successfully"),
     *             @OA\Property(property="event", ref="#/components/schemas/Event")
     *         ),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={
     *                  "name": {"The name field is required."},
     *                  "location": {"The location field is required."},
     *                  "city": {"The city field is required."},
     *                  "date_start": {"The date_start field is required."},
     *                  "time": {"The time field is required."},
     *                  "owner_id": {"The owner_id field is required."},
     *                  "number_of_space": {"The number_of_space field is required."},
     *                  "number_of_table": {"The number_of_table field is required."},
     *                  "event_type_id": {"The event_type_id field is required."}
     *              }),
     *         ),
     *     ),
     * )
     *
     * @OA\Schema(
     *      schema = "Event",
     *      required={"name", "location","city","date_start","time", "owner_id","number_of_space","number_of_table", "event_type_id"},
     *             @OA\Property(property="name", type="string", description="Event's name"),
     *             @OA\Property(property="location", type="string", description="Event's location"),
     *             @OA\Property(property="description", type="string", description="Event's description"),
     *             @OA\Property(property="city", type="string", description="Event's city"),
     *             @OA\Property(property="status", type="enum", description="Event's status"),
     *             @OA\Property(property="rest_of_space", type="string", description="Event's rest_of_space"),
     *             @OA\Property(property="created_at", type="string", description="Event's created_at"),
     *             @OA\Property(property="rest_of_table", type="string", description="Event's rest_of_table"),
     *             @OA\Property(property="updated_at", type="string", description="Event's updated_at"),
     *             @OA\Property(property="date_start", type="string", description="Event's date_start"),
     *             @OA\Property(property="time", type="string", description="Event's time"),
     *             @OA\Property(property="neighborhood", type="string", description="Event's neighborhood"),
     *             @OA\Property(property="owner_id", type="string", description="Event's owner_id"),
     *             @OA\Property(property="number_of_space", type="string", description="Event's number_of_space"),
     *             @OA\Property(property="number_of_table", type="string", description="Event's number_of_table"),
     *             @OA\Property(property="event_type_id", type="string", description="Event's event_type_id"),
     * )
     */

    //add new event
    public function addEvent(Request $request)
    {
        // calidation
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|min:2|max:250|unique:events',
            'location' => 'required|string|min:2|max:250',
            'description' => 'string|max:250',
            'city'  => 'required|string|max:50',
            'date_start' => 'required|string',
            'time' => 'required|string',
            'neighborhood' => 'string|max:50',
            'owner_id' => 'required|exists:owners,id',
            'event_type_id' => 'required|exists:event_types,id',
            'number_of_space' => 'required|integer',
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json([
                "errors" => $errors,
                "status" => 401,
            ]);
        }

        if ($validation->passes()) {
            if (auth()->check() && (auth()->user()->role->name == 'admin' || auth()->user()->role->name == 'évènementiel')) {
                $date = Carbon::parse($request->date_start);
                $formattedDate = $date->toDateString();
                $event = Event::create([
                    "name" => $request->name,
                    "location" => $request->location,
                    "description" => $request->description,
                    "city" => $request->city,
                    "date_start" => $formattedDate,
                    "time" => $request->time,
                    "neighborhood" => $request->neighborhood,
                    'owner_id' => $request->owner_id,
                    'event_type_id' => $request->event_type_id,
                    'number_of_space' => $request->number_of_space,
                ]);

                $gerer = Gerer::create([
                    "user_id" => Auth::user()->id,
                    "event_id" => $event->id
                ]);

                return response()->json([
                    "message" => "Event created sucessfuly",
                    "data" => $event,
                    "user_data" => $gerer,
                    "status" => 201,
                ]);
            } else {
                return response()->json(['error' => 'Unauthorized'], 402);
            }
        }
    }

    public function verification(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code_name' => 'string|required|digits:4'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'error' => $validator->errors(),
            ], 422);
        }


        $code = Code::where('code_name', $request->code_name)
            ->first();


        if ($code) {
            $event = DB::table('codes')
                ->join('events', 'codes.event_id', '=', 'events.id')
                ->select(
                    'events.id',
                    'events.name',
                    'codes.id',
                    'codes.code_name',
                )
                ->where('events.id', $code->event_id)
                ->first();

            if ($event) {
                return response()->json([
                    "Connection reussi. vous controller l'evennement " . $event->name,
                    $event
                ], 200);
            } else {
                return response()->json([
                    "Le code de verification a deja ete generer pour cet evennement",
                ], 423);
            }
        } else {
            return response()->json([
                "Code de verification incorrect",
            ], 424);
        }
    }


    public function generateCode($event_id)
    {
        // test user role
        $user = auth()->user();
        if ($user) {
            if (auth()->user()->role->value === 1 || 2) {

                $code = random_int(1000, 9999);

                $codeExists = Code::where('event_id', $event_id)->where('is_generate', true)->exists();
                if (!$codeExists) {
                    $event_type =  Code::create([
                        // 'code_name' => Hash::make($code),
                        'code_name' => $code,
                        'event_id' => $event_id,
                        'is_generate' => true
                    ]);

                    $getCode = Code::where('event_id', $event_id)->where('code_name', $code)->get();
                } else {
                    return response()->json([
                        'error' => 'Un code de verification a déja  été creér pour cet évennement.'
                    ], 402);
                }

                return response()->json([
                    +"code" => $getCode,
                    'Code generate successful',
                ], 200);
            } else {
                return response()->json([
                    'error' => 'Vous n\'etes pas autoriser a effectuer cette action'
                ], 403);
            }
        } else {
            return response()->json([
                'error' => 'Utilisateur non authentifier'
            ], 405);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/event/updateEvent/{event}",
     *     tags={"event"},
     *     summary="Update a single event",
     *     description="Update details of an existing event",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the event to update",
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         description="Updated event details",
     *         @OA\JsonContent(
     *             required={"name", "location","city","date_start","time", "owner_id","number_of_space","number_of_table", "event_type_id"},
     *             @OA\Property(property="name", type="string", description="Event's name"),
     *             @OA\Property(property="location", type="string", description="Event's location"),
     *             @OA\Property(property="description", type="string", description="Event's description"),
     *             @OA\Property(property="city", type="string", description="Event's city"),
     *             @OA\Property(property="date_start", type="string", description="Event's date_start"),
     *             @OA\Property(property="time", type="string", description="Event's time"),
     *             @OA\Property(property="neighborhood", type="string", description="Event's neighborhood"),
     *             @OA\Property(property="owner_id", type="string", description="Event's owner_id"),
     *             @OA\Property(property="number_of_space", type="string", description="Event's number_of_space"),
     *             @OA\Property(property="number_of_table", type="string", description="Event's number_of_table"),
     *             @OA\Property(property="event_type_id", type="string", description="Event's event_type_id"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Event updated successfully"),
     *             @OA\Property(property="event", ref="#/components/schemas/Event"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation error"),
     *             @OA\Property(property="errors", type="object", example={
     *                "name": {"The name field is required."},
     *                  "location": {"The location field is required."},
     *                  "city": {"The city field is required."},
     *                  "date_start": {"The date_start field is required."},
     *                  "time": {"The time field is required."},
     *                  "owner_id": {"The owner_id field is required."},
     *                  "number_of_space": {"The number_of_space field is required."},
     *                  "number_of_table": {"The number_of_table field is required."},
     *                  "event_type_id": {"The event_type_id field is required."}
     *             }),
     *         ),
     *     ),
     * )
     */

    // update event detail
    public function updateEvent(Request $request, $event_Id)
    {
        // get user id
        $user = Auth()->user();
        $validation = Validator::make($request->all(), [
            'name' => 'required|string|max:20',
            'location' => 'required|string|max:50',
            'description' => 'string',
            'city'  => 'required|string|max:20',
            'date_start' => 'required|string|date',
            'time' => 'required|string',
            'neighborhood' => 'string|max:50',
            'owner_id' => 'required|exists:owners,id',
            'event_type_id' => 'required|exists:event_types,id',
        ]);

        if ($validation->fails()) {
            $errors = $validation->errors();

            return response()->json([
                "errors" => $errors,
                "status" => 402,
            ], 402);
        }

        // Retrieve the event from the database
        $event = Event::find($event_Id);

        if (!$event) {
            return response()->json([
                'error' => 'Event not found'
            ], 404);
        }

        // if ($validation->passes()) {
        //     if (auth()->user()->role->name == 'admin' || 'évènementiel') {
        // Update the enevt's properties with the new data
        $event->name = $request->name;
        $event->location = $request->location;
        $event->description = $request->description;
        $event->city = $request->city;
        $event->date_start = $request->date_start;
        $event->time = $request->time;
        $event->neighborhood = $request->neighborhood;
        $event->owner_id = $request->owner_id;
        $event->event_type_id = $request->event_type_id;

        // Save the changes to the database
        $event->update();
        return response()->json([
            "message" => "Événement mis à jour avec succès.",
        ]);
        //     }else{
        //         return response()->json([
        //             "message" => "Vous n'avez pas le droit neccessaire pour effectuer cette action.",
        //         ] , 201);
        //     }
        // }
    }

    /**
     * @OA\Put(
     *     path="/api/event/updateEventStatus/{event}",
     *     tags={"event"},
     *     summary="Update event status",
     *     description="Update an existing event by ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Id of the event",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Guest updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Event cancelled successfully"),
     *             @OA\Property(property="guest", ref="#/components/schemas/Guest"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Guest not found",
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="The event is in progress cannot change the status.",
     *     ),
     *     @OA\Response(
     *         response=421,
     *         description="he event is already canceled.",
     *     ),
     *     ),
     * )
     *
     */
    // update event status
    public function updateEventStatus($event_Id)
    {
        // Retrieve the event from the database
        $event = Event::find($event_Id);
        if (!$event) {
            return response()->json([
                'error' => 'Evennement non trouver'
            ], 404);
        }

        if (auth()->user()->role->name === 'admin' || 'évènementiel') {

            switch ($event->status) {
                case "En attente":
                    // update status
                    $event->update(['status' => 'Annuler']);
                    return response()->json([
                        'message' => 'Evennement annuler avec succes',
                        'status' => $event->status
                    ], 201);
                    break;

                case "Annuler":
                    // handle error message
                    return response()->json([
                        'message' => 'Evennement deja annuler impossible de l\'annuler.',
                        'status' => $event->status
                    ], 423);
                    break;

                case "Terminer":
                    // handle error message
                    return response()->json([
                        'message' => 'Evennement deja terminer impossible de l\'annuler.',
                        'status' => $event->status
                    ], 423);
                    break;

                default:
                    // else do this
                    return response()->json([
                        'message' => 'Cet evennement est en cours impossible de l\'annuler.',
                        'status' => $event->status
                    ], 424);
            }
        } else {
            return response()->json([
                'message' => 'Vous n\'avez pas le droit nessessaire pour effectuer cette action.'
            ], 401);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/event/getAllEvent",
     *     tags={"event"},
     *     summary="Get all events",
     *     description="Retrieve a list of all events",
     *     @OA\Response(
     *         response=200,
     *         description="List of events",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Event"),
     *         ),
     *     ),
     * )
     *
     *
     */

    // get all event
    public function getAllEvent()
    {

        // $event = DB::table('events')
        //     ->join('owners', 'events.owner_id', '=', 'owners.id')
        //     ->join('event_types', 'events.event_type_id', '=', 'event_types.id')
        //     ->select('events.*', 'owners.firstname', 'event_types.name')
        //     ->get();

        $event = DB::table('events')
            ->join('owners', 'events.owner_id', '=', 'owners.id')
            ->join('event_types', 'events.event_type_id', '=', 'event_types.id')
            ->select(
                'events.id',
                'events.name',
                'events.location',
                'events.description',
                'events.date_start',
                'events.time',
                'events.status',
                'events.city',
                'events.neighborhood',
                'events.number_of_space',
                'events.rest_of_space',
                'events.guest_present',
                'events.created_at',
                'events.updated_at',
                'owners.firstname as owner_firstname',
                'owners.lastname as owner_lastname',
                'event_types.name as event_type',
            )
            ->orderBy('events.name', 'asc')
            ->paginate(20);

        // $event = Event::orderBy('name', 'asc')->paginate(20);
        return response()->json($event);
    }

    /**
     * @OA\Get(
     *     path="/api/event/showEvent/{event}",
     *     tags={"event"},
     *     summary="Show event details",
     *     description="Retrieve details of a single event by its ID",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="ID of the event",
     *         @OA\Schema(type="string"),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Event details retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Event details retrieved successfully"),
     *             @OA\Property(property="event", ref="#/components/schemas/Event"),
     *         ),
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Event not found",
     *     ),
     * )
     */

    // Sho single event detail
    public function showEvent($id)
    {
        $event = DB::table('events')
            ->join('owners', 'events.owner_id', '=', 'owners.id')
            ->join('event_types', 'events.event_type_id', '=', 'event_types.id')
            ->select(
                'events.id',
                'events.name',
                'events.location',
                'events.description',
                'events.date_start',
                'events.city',
                'events.time',
                'events.city',
                'events.neighborhood',
                'events.number_of_space',
                'events.rest_of_space',
                'events.guest_present',
                'events.status',
                'events.created_at',
                'events.updated_at',
                'owners.firstname  as owners_firstname',
                'owners.lastname as owners_lastname',
                'event_types.name as event_type'
            )
            ->where('events.id', $id)
            ->get();
        $getCode = Code::where('event_id', $id)->get();

        // $event = Event::with(['owners', 'event_types'])
        //     ->select('name', 'location', 'date_start', 'city', 'owners.firstname', 'event_types.name')
        //     ->where('events.id', $id)
        //     ->get();

        return response()->json([
            'data' => $event,
            "code" => $getCode,
            'status' => 200
        ],);
    }

    public function getAllEventType()
    {
        $event_type = Event_type::orderBy('name', 'asc')->get();
        return response()->json([
            'Event_type' => $event_type
        ]);
    }

    public function getAllOwner()
    {
        $owner = Owner::orderBy('firstname', 'asc')->get();
        return response()->json([
            'Owner' => $owner
        ]);
    }
}
