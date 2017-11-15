<?php
namespace App\Destiny;

use Illuminate\Database\Eloquent\Model;

class BungieNetAccount extends Model
{
    protected $fillable  = ['membershipId', 'uniqueName', 'displayName'];
}