<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function facebookPages()
    {
        return $this->hasMany(FacebookPage::class);
    }

    public function conversations()
    {
        return $this->hasMany(Conversation::class);
    }

    public function savedChats()
    {
        return $this->hasMany(SavedChat::class);
    }
}