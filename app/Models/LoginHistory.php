<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginHistory extends Model
{
    protected $fillable = ['user_id', 'email', 'ip_address', 'user_agent', 'successful'];

    protected function casts(): array
    {
        return [
            'successful' => 'boolean',
        ];
    }
}
