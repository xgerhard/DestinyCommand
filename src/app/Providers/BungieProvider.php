<?php
namespace App\Providers;

use App\Destiny\DestinyClient;
use App\Destiny\Profile;
use App\Destiny\Filters\StatsFilter;

class BungieProvider
{
    public function __construct()
    {
        $this->destiny = new DestinyClient;
    }

    function fetch($oAction, $aParameters, $bPrepare = false)
    {
        switch($oAction->endpoint)
        {
            case 'profile':

                if($bPrepare === true)
                {
                    $aComponents = $oAction->options->params['components'] ?? array();
                    $aComponents = array_merge($aComponents, array(200)); // 200 is characters, we always need this.

                    foreach($aParameters['players'] AS $oPlayer)
                    {
                        $this->destiny->getProfile($oPlayer->membershipType, $oPlayer->membershipId, $aComponents);
                    }
                }
                else
                {
                    $aProfiles = $this->destiny->get('getProfile');
                    foreach($aProfiles AS $x => $aProfile)
                    {
                        $aProfile = new Profile($aProfile);
                        $aProfiles[$x] = $aProfile->{$oAction->filter}($oAction->options);
                    }
                    return $aProfiles;
                }
            break;

            case 'stats':
                if($bPrepare === true)
                {
                    if(isset($oAction->options->seperate) && $oAction->options->seperate === true)
                    {
                        $aCharacters = [];
                        foreach($aParameters['players'] AS $oPlayer)
                        {
                            $this->destiny->getProfile($oPlayer->membershipType, $oPlayer->membershipId, array(200));
                        }
                        $aProfiles = $this->destiny->get('getProfile');
                        foreach($aProfiles AS $oProfile)
                        {
                            foreach($oProfile->characters->data AS $oCharacter)
                            {
                                $aCharacters[$oCharacter->characterId] = $oCharacter->classHash;
                                $this->destiny->getHistoricalStats($oCharacter->membershipType, $oCharacter->membershipId, $oCharacter->characterId, ['modes' => $oAction->options->modes]);
                            }
                        }
                        return $aCharacters;
                    }
                    else
                    {
                        foreach($aParameters['players'] AS $oPlayer)
                        {
                            $this->destiny->getHistoricalStats($oPlayer->membershipType, $oPlayer->membershipId, 0, ['modes' => $oAction->options->modes]);
                        }
                    }
                }
                else
                {
                    $aRes = [];
                    $aStatsRes = $this->destiny->get('getHistoricalStats');
                    foreach($aStatsRes AS $x => $oStatsObject)
                    {
                        $aIds = explode("-", $x);
                        $oStatsFilter = new StatsFilter($oStatsObject);
                        $aRes[$aIds[0] .'-'. $aIds[1]][$aIds[2]] = $oStatsFilter->getStats($oAction->options->field);
                    }
                    return $aRes;
                }
            break;
        }
    }
}
?>