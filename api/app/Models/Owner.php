<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Owner extends Model
{
    use HasUuids;
    use HasFactory;
    use SoftDeletes;
    protected $table = 'owners';
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'phone',
        'gender',
        'description',
        'adresse'
    ];

    // public function event() : HasMany
    // {
    //     return $this->withTrashed()
    //     ->HasMany(Event::class ,'owner_id', 'id');
    // }
}
