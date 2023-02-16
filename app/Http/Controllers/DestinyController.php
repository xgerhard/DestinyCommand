<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use App\Services\Destiny\DestinyService;

class DestinyController extends BaseController
{
    public function destiny(DestinyService $destiny)
    {
        dd($destiny->getProfile(3, 4611686018467322796, [100, 200]));
        $data = $destiny->getHistoricalStats(3, 4611686018467322796, 2305843009301405871, [
            'groups' => 'Weapons,Medals',
            'modes' => 'AllPvP,Raid',
        ]);

        foreach ($data as $mode => $stats) {
            echo "<h1>Mode: $mode</h1><ul>";
            foreach ($stats as $periodType => $periodStats) {
                echo "<li>Period: $periodType<ul>";
                foreach ($periodStats as $statId => $stat) {
                    echo "<li>$statId: {$stat->getDisplayValue()}</li>";
                }
                echo "</ul></li>";
            }
            echo "</ul>";
        }
        die;

        
    }
}
