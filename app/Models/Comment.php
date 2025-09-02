<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comment extends Model
{
    protected $fillable = [
        'issue_id',
        'content',
    ];

    public function issue(): BelongsTo
    {
        return $this->belongsTo(Issue::class);
    }
}

