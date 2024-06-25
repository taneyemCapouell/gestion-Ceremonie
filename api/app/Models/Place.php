<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Place extends Model
{
    use HasUuids;
    use HasFactory;
    protected $table = 'places';
    protected $fillable = [
        'name',
    ];

    public function table(): BelongsTo
    {
        return $this->withTrashed()->belongsTo(Table::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class);
    }
}
