<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Code extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'codes';
    protected $fillable = [
        'code_name',
        'event_id',
        'is_generate',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
