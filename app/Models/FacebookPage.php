<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class FacebookPage extends Model
{
    protected $fillable = [
        'user_id',
        'page_id',
        'page_name',
        'page_access_token',
        'page_profile_pic',
        'is_active',
        'connected_at'
    ];

    protected $casts = [
        'user_id' => 'integer',
        'connected_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Encrypt and decrypt page access token
     */
    protected function pageAccessToken(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $value ? Crypt::decryptString($value) : null,
            set: fn ($value) => $value ? Crypt::encryptString($value) : null,
        );
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class, 'page_id');
    }
}