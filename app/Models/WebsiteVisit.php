<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WebsiteVisit extends Model
{
    protected $fillable = [
        'user_id',
        'visitor_hash',
        'session_hash',
        'path',
        'route_name',
        'locale',
        'source_host',
        'device',
        'browser',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
