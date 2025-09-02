<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class Project extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'deadline',
        'owner_id',
    ];

    public function issues(): HasMany
    {
        return $this->hasMany(Issue::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }
}
