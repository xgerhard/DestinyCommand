<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Services\Destiny\DestinyService;

class DestinyController extends BaseController
{
    public function destiny(DestinyService $destiny)
    {
        dd($destiny->getHistoricalStats(3, 4611686018467322796, 2305843009301405871, ['groups' => 'general']));
        dd($destiny->getProfile(3, 4611686018467322796, [100, 200]));
    }
}
