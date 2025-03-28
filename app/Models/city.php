<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class city extends Model
{
    protected $fillable = [
        'name',
        'city_id',
        'state_id'
    ];
    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
    public function state(): BelongsTo
    {
        return $this->belongsTo(state::class);
    }
}
