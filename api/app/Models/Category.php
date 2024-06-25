<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasUuids;
    use HasFactory;
    protected $table = 'categories';
    protected $fillable = [
        'name',
    ];

    public function table() :HasMany
    {
        return $this->withTrashed()
        ->hasMany(Table::class);
    }
}
