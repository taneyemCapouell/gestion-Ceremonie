<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event_type extends Model
{
    use HasUuids;
    use HasFactory;
    use SoftDeletes;

    protected $table = 'event_types';
    protected $fillable = [
        'name',
    ];

    // public function event() : HasMany
    // {
    //     return $this->withTrashed()
    //     ->hasMany(Event::class ,  'event_type_id', 'id');
    // }
}
