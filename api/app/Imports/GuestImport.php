<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\Guest;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class GuestImport implements ToModel , WithHeadingRow
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        $event = Event::where('name', $row['name'])->first();

        if ($event) {
            $event->rest_of_space = $event->rest_of_space - 1;
            $event->save();
        }
        return new Guest([
            'firstname' => $row['firstname'],
            'lastname' => $row['lastname'],
            'email' => $row['email'],
            'phone' => $row['phone'],
            'gender' => $row['gender'],
            'event_id' => $event->id
        ]);
    }
}
