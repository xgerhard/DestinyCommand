<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class UserPlayer extends Model 
{
    protected $fillable = ['provider', 'providerId', 'destinyPlayerId', 'bungieNetAccountId'];

    public function destinyPlayer()
    {
        return $this->hasOne('App\Destiny\DestinyBungiePlayer', 'id', 'destinyPlayerId');
    }

    public function BungieNetAccount()
    {
        return $this->hasOne('App\Destiny\BungieNetAccount', 'id', 'bungieNetAccountId');
    }
}