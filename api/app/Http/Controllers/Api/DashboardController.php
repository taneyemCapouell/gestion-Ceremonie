<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Guest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    //total if users
    public function totalUser()
    {
        $totalUsers = User::count();
        return response()->json([
            'users' => $totalUsers
        ]);
    }

    //total if event
    public function totalEvent()
    {
        $totalEvents = Event::count();
        return response()->json([
            'events' => $totalEvents
        ]);
    }

    //total of event end
    public function eventEnd()
    {
        $EventEnds = Event::where('status', 'Termine')->count();
        return response()->json([
            'eventEnd' => $EventEnds
        ]);
    }

    //total of event in hold
    public function eventOnHold()
    {
        $EventOnHolds = Event::where('status', 'En attente')->count();
        return response()->json([
            'eventInHold' => $EventOnHolds
        ]);
    }

    public function listOfEventOnHold()
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
                'events.time',
                'events.status',
                'events.city',
                'events.neighborhood',
                'events.number_of_space',
                'events.number_of_table',
                'events.rest_of_space',
                'events.rest_of_table',
                'events.created_at',
                'events.updated_at',
                'owners.firstname as owner_firstname',
                'owners.lastname as owner_lastname',
                'event_types.name as event_type',
            )
            ->where('events.status', 'En attente')
            ->orderBy('events.name', 'asc')
            ->paginate(20);

        // $event = Event::orderBy('name', 'asc')->paginate(20);
        return response()->json($event);
    }
    //total of event canceled
    public function eventCancel()
    {
        $EventCanceled = Event::where('status', 'Annule')->count();
        return response()->json([
            'eventCanceled' => $EventCanceled
        ]);
    }


    //total of event in progress
    public function eventInProgress()
    {
        $eventInProgress = Event::where('status', 'En cours')->count();
        if (!$eventInProgress) {
            return response()->json(['message' => 'No event in progress'], 404);
        }

        return response()->json([
            'eventInProgress' => $eventInProgress
        ]);
    }



    //total of guest in hold
    public function guestInHold()
    {
        $guestInHold = Guest::where('status', '1')->count();
        return response()->json([
            'Total of guest in hold' => $guestInHold
        ]);
    }

    //total of guest confirmed
    public function guestConfirmed()
    {
        $guestConfirmed = Guest::where('status', '2')->count();
        return response()->json([
            'guestConfirmed' => $guestConfirmed
        ]);
    }
    //total of guest present
    public function guestPresent()
    {
        $guestPresent = Guest::where('status', '3')->count();
        return response()->json([
            'guestPresent' => $guestPresent
        ]);
    }
    //total of guest absent
    public function guestAbsent()
    {
        $guestAbsent = Guest::where('status', '4')->count();
        return response()->json([
            'Total of guest absent' => $guestAbsent
        ]);
    }



    //total event guests
    public function totalGuestInEvent()
    {
        $eventInProgress = Event::where('status', 'En cours')->first();
        if (!$eventInProgress) {
            return response()->json(['message' => 'No event in progress'], 404);
        }

        $totalGuestInEvent = Guest::where('event_id', $eventInProgress->id)->count();
        // $totalGuestInEvent = DB::table('guests')
        //     ->join('events', 'guests.event_id', '=', 'events.id')
        //     ->where('event_id', $eventInProgress->id)
        //     ->count();

        return response()->json([
            'Total event guests' => $totalGuestInEvent
        ]);
    }

    //total event guests present
    public function numberOfGuestPresent()
    {
        $eventInProgress = Event::where('status', 'En cours')->first();
        if (!$eventInProgress) {
            return response()->json(['message' => 'No event in progress'], 404);
        }

        $numberOfGuestPresent = Guest::where('event_id', $eventInProgress->id)->where('status', '3')->count();

        // $numberOfGuestPresent = DB::table('guests')
        //     ->join('events', 'guests.event_id', '=', 'events.id')
        //     ->where('event_id', $eventInProgress->id)
        //     ->where('guests.status', 3)
        //     ->count();

        return response()->json([
            'Total event guests present' => $numberOfGuestPresent
        ]);
    }

    //total of table available from event in progress
    public function numberOfTableAvailable()
    {
        $eventInProgress = Event::where('status', 'En cours')->first();
        if (!$eventInProgress) {
            return response()->json(['message' => 'No event in progress'], 404);
        }

        $numberOfTableAvailable = $eventInProgress->number_of_table;

        return response()->json([
            'tablesAvailableProgress' => $numberOfTableAvailable
        ]);
    }

    //total of place available from event in progress
    public function numberOfPlaceAvailable()
    {
        $eventInProgress = Event::where('status', 'En cours')->first();
        if (!$eventInProgress) {
            return response()->json(['message' => 'No event in progress'], 404);
        }

        $numberOfPlaceAvailable = $eventInProgress->rest_of_space;

        return response()->json([
            'placesAvailableProgress' => $numberOfPlaceAvailable
        ]);
    }

    //total of guests
    public function totalGuest()
    {
        $totalGuests = Guest::count();
        return response()->json([
            'totalGuests' => $totalGuests
        ]);
    }
}
