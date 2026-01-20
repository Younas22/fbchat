<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedChat extends Model
{
    protected $fillable = [
        'conversation_id',
        'user_id',
        'chat_id',
        'notes',
        'saved_at'
    ];

    protected $casts = [
        'saved_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($savedChat) {
            if (!$savedChat->saved_at) {
                $savedChat->saved_at = now();
            }
        });
    }

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}