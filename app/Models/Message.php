<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'conversation_id',
        'message_id',
        'message_text',
        'sender_type',
        'sender_id',
        'attachment_type',
        'attachment_url',
        'status',
        'sent_at'
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function conversation()
    {
        return $this->belongsTo(Conversation::class);
    }
}
