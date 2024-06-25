<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Table extends Model
{
    use HasUuids;
    use HasFactory;
    use SoftDeletes;
    protected $table = 'tables';
    protected $fillable = [
        'name',
        'capacity',
        'status',
        'rest_of_place',
        'guests',
        'qr_code_path',
        'event_id',
        'categorie_id'
    ];

    /**
     * Get the place.
     */
    public function place(): HasMany
    {
        return $this->withTrashed()
            ->hasMany(Place::class);
    }

    /**
     * Get the category.
     */
    public function categorie()
    {
        return $this->withTrashed()->belongsTo(Category::class);
    }

    /**
     * Get event.
     */
    public function events()
    {
        return $this->belongsToMany(Event::class);
    }


}
