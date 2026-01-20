<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Conversation extends Model
{
    protected $fillable = [
        'user_id',
        'page_id',
        'conversation_id',
        'customer_name',
        'customer_psid',
        'customer_fb_id',
        'customer_profile_pic',
        'last_message_preview',
        'last_message_time',
        'is_archived'
    ];

    protected $dates = ['last_message_time'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function page()
    {
        return $this->belongsTo(FacebookPage::class, 'page_id');
    }

    public function savedChats()
    {
        return $this->hasMany(SavedChat::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}