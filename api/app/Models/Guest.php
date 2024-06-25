<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Guest extends Model
{
    use HasUuids;
    use HasFactory;
    use SoftDeletes;
    protected $table = 'guests';
    protected $fillable = [
        'firstname',
        'lastname',
        'phone',
        'email',
        'gender',
        'status',
        'place_id',
        'table_id',
        'event_id'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function place() : BelongsTo
    {
        return $this->withTrashed()
        ->belongsTo(Place::class , 'place_id',);
    }
}
