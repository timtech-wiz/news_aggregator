<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FetchLog extends Model
{
    protected $fillable = [
        'source_api',
        'articles_fetched',
        'articles_saved',
        'duplicates',
        'status',
        'error_message',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
    ];
}
