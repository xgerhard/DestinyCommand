<?php
namespace App\Destiny;

use Illuminate\Database\Eloquent\Model;

class DestinyPlayer extends Model
{
    protected $fillable  = ['id', 'membershipId', 'membershipType', 'displayName', 'account_id', 'is_default'];
}