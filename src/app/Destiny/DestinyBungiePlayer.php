<?php
namespace App\Destiny;

use Illuminate\Database\Eloquent\Model;

class DestinyBungiePlayer extends Model
{
    protected $fillable  = [
        'membership_id',
        'membership_type',
        'display_name',
        'display_code'
    ];
}