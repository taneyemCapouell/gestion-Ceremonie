<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contienir extends Model
{
    use HasFactory;
    use SoftDeletes;
    use HasUuids;

    protected $table = 'contienirs';
    protected $fillable = [
        'event_id',
        'guest_id',
    ];

    public function event(): BelongsTo
    {
        return $this->withTrashed()
        ->belongsTo(Event::class);
    }

    public function guest(): BelongsTo
    {
        return $this->withTrashed()
        ->belongsTo(Guest::class);
    }
}
