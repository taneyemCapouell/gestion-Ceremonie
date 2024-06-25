<?php

namespace App\Models;

use App\Enums\EventStatus;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasUuids;
    use HasFactory;
    use SoftDeletes;
    protected $keyType = 'string';
    public $incrementing = false;
    protected $table = 'events';
    protected $fillable = [
        'name',
        'description',
        'location',
        'date_start',
        'time',
        'status',
        'city',
        'neighborhood',
        'owner_id',
        'event_type_id',
        "number_of_space",
        "rest_of_space",
        "number_of_table",
        "rest_of_table"
    ];

    // const PENDING = 'pending';//En attente
    // const ONGOING = 'ongoing';//En cours
    // const COMPLETED = 'completed';//Terminer
    // const CANCELLED = 'Cancelled';//Annuler


    public function ownner() : BelongsTo
    {
        return $this->withTrashed()
        ->belongsTo(Owner::class , 'owner_id');
    }

     /**
     * Get guest.
     */
    public function guests()
    {
        return $this->hasMany(Guest::class);
    }

    /**
     * Get tables.
     */
    public function tables()
    {
        return $this->belongsToMany(Table::class);
    }

        /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];



}
