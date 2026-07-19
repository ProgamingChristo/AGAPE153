<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppVerificationCode extends Model
{
    protected $table = 'whatsapp_verification_codes';

    protected $fillable = [
        'phone',
        'code',
        'purpose',
        'payload',
        'attempts',
        'expires_at',
        'verified_at',
    ];

    protected function casts(): array
    {
        return [
            'payload' => 'array',
            'expires_at' => 'datetime',
            'verified_at' => 'datetime',
        ];
    }
}
