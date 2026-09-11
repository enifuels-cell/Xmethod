<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportCase extends Model
{
    protected $fillable = [
        'email',
        'username',
        'message',
        'new_email',
        'status',
        'access_enabled',
    ];

    protected $casts = [
        'access_enabled' => 'boolean',
    ];

    public function messages()
    {
        return $this->hasMany(SupportMessage::class);
    }
}