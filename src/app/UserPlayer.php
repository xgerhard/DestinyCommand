<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPlayer extends Model 
{
    protected $fillable = ['provider', 'providerId', 'destinyPlayerId'];

    public function destinyPlayer()
    {
        return $this->hasOne('App\Destiny\DestinyPlayer', 'id', 'destinyPlayerId');
    }
}