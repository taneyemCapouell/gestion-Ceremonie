<?php

use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\GuestController;
use App\Http\Controllers\Api\OwnerController;
use App\Http\Controllers\Api\TableController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', [AuthController::class, 'user']);
});


// ROUTE
Route::group(['middleware' => 'api'], function () {

    // AUTH ROUTE
    Route::group(['prefix' => 'auth'], function ($router) {

        Route::post('register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/profile', [AuthController::class, 'profile']);
        Route::post('/updateUserPassword', [AuthController::class, 'updateUserPassword']);
    });

    // Protected route
    Route::group(['prefix' => 'user'], function () {
        // Route::middleware(['checkUserRole:' . Role::ADMIN])->group(['prefix' => 'user'] , function () {

        // users
        Route::post('store/', [UserController::class, 'store']);
        Route::get('getRole/', [UserController::class, 'getRole']);
        Route::get('showUser/{user}', [UserController::class, 'showUser']);
        Route::put('updateUser/{user}', [UserController::class, 'updateUser']);
        Route::get('getAllUser/', [UserController::class, 'getAllUser']);
        Route::post('/toggleStatus/{user}', [UserController::class, 'toggleStatus']);
        Route::put('/upload_photo', [UserController::class, 'upload_photo']);
    });

    // dashboard
    Route::group(['prefix' => 'dashboard'], function () {
        Route::get('/totalUser', [DashboardController::class, 'totalUser']);

        Route::get('/totalEvent', [DashboardController::class, 'totalEvent']);
        Route::get('/eventEnd', [DashboardController::class, 'eventEnd']);
        Route::get('/eventInProgress', [DashboardController::class, 'eventInProgress']);
        Route::get('/eventOnHold', [DashboardController::class, 'eventOnHold']);
        Route::get('/listOfEventOnHold', [DashboardController::class, 'listOfEventOnHold']);
        Route::get('/eventCancel', [DashboardController::class, 'eventCancel']);

        Route::get('/totalGuestInEvent', [DashboardController::class, 'totalGuestInEvent']);
        Route::get('/numberOfGuestPresent', [DashboardController::class, 'numberOfGuestPresent']);
        Route::get('/numberOfTableAvailable', [DashboardController::class, 'numberOfTableAvailable']);
        Route::get('/numberOfPlaceAvailable', [DashboardController::class, 'numberOfPlaceAvailable']);

        Route::get('/totalGuest', [DashboardController::class, 'totalGuest']);
        Route::get('/guestInHold', [DashboardController::class, 'guestInHold']);
        Route::get('/guestPresent', [DashboardController::class, 'guestPresent']);
        Route::get('/guestConfirmed', [DashboardController::class, 'guestConfirmed']);
        Route::get('/guestAbsent', [DashboardController::class, 'guestAbsent']);

        Route::get('/totalTable', [DashboardController::class, 'totalTable']);
        Route::get('/tableAvailable', [DashboardController::class, 'tableAvailable']);
        Route::get('/tableOccupy', [DashboardController::class, 'tableOccupy']);

        Route::get('/placeAvailable', [DashboardController::class, 'placeAvailable']);
    });

    Route::group(['prefix' => 'owner'], function () {

        // Owner
        Route::post('addOwner/', [OwnerController::class, 'addOwner']);
        Route::get('showOwner/{owner}', [OwnerController::class, 'showOwner']);
        Route::put('updateOwner/{owner}', [OwnerController::class, 'updateOwner']);
        Route::get('getAllOwner/', [OwnerController::class, 'getAllOwner']);
    });

    Route::group(['prefix' => 'event'], function () {
        // Event
        Route::post('/verification', [EventController::class, 'verification']);
        Route::post('generateCode/{event}', [EventController::class, 'generateCode']);
        Route::post('addEventType/', [EventController::class, 'addEventType']);
        Route::post('addEvent/', [EventController::class, 'addEvent']);
        Route::get('getAllEventType/', [EventController::class, 'getAllEventType']);
        Route::get('getAllOwner/', [EventController::class, 'getAllOwner']);
        Route::get('showEvent/{event}', [EventController::class, 'showEvent']);
        Route::get('getAllEvent/', [EventController::class, 'getAllEvent']);
        Route::put('updateEvent/{event}', [EventController::class, 'updateEvent']);
        Route::put('updateEventStatus/{event}', [EventController::class, 'updateEventStatus']);
    });

    Route::group(['prefix' => 'table'], function () {
        // Table
        Route::post('addPlace/', [TableController::class, 'addPlace']);
        Route::post('addCategory/', [TableController::class, 'addCategory']);
        Route::post('attributePlace/{place}', [TableController::class, 'attributePlace']);
        Route::post('addTable/', [TableController::class, 'addTable']);
        Route::get('showTable/{table}', [TableController::class, 'showTable']);
        Route::get('getAllCategory', [TableController::class, 'getAllCategory']);
        Route::get('getAllPlace', [TableController::class, 'getAllPlace']);
        Route::get('getAllTable/', [TableController::class, 'getAllTable']);
        Route::put('updateTable/{table}', [TableController::class, 'updateTable']);

        // import

        Route::post('/import/place', [TableController::class, 'importPlace']);
        Route::post('/import/table', [TableController::class, 'importTable']);
        // export

        Route::get('/file-import', [TableController::class, 'importView']);
        Route::get('/export-products', [TableController::class, 'exportUsers']);
    });


    Route::group(['prefix' => 'guest'], function () {
        // Guest
        Route::post('addGuest/', [GuestController::class, 'addGuest']);
        Route::put('updateGuest/{guest}', [GuestController::class, 'updateGuest']);
        Route::get('showGuest/{guest}', [GuestController::class, 'showGuest']);
        Route::get('getAllGuest/', [GuestController::class, 'getAllGuest']);
        Route::get('getAllTableInEvent/{event}', [GuestController::class, 'getAllTableInEvent']);
        Route::get('getAllPlaceInTable/{table}', [GuestController::class, 'getAllPlaceInTable']);
        Route::put('updateStatus/{guest}', [GuestController::class, 'updateStatus']);
        Route::post('/toggle_status/{guest}', [GuestController::class, 'toggleStatusGuest']);

        // import
        Route::post('/import/guest', [GuestController::class, 'importGuest']);

        // export
        Route::get('/export-guest', [GuestController::class, 'exportUsers']);
    });
});
