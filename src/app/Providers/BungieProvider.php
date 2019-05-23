<?php
namespace App\Providers;

use Cache;
use App\Destiny\DestinyClient;
use App\Destiny\Profile;
use App\Destiny\Filters\StatsFilter;
use App\Destiny\Vendor;

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
            case 'vendor':

                $strCacheKey = 'vendor-'. $oAction->options->hash;
                $bCache = Cache::has($strCacheKey);

                if($bPrepare === true)
                {
                    if(!$bCache)
                        $this->destiny->getPublicVendors($oAction->options->params['components'] ?? []);
                }
                else
                {
                    if(!$bCache)
                    {
                        $oVendors = $this->destiny->get('getPublicVendors')['getPublicVendors'];
                        //$oVendor = new Vendor;
                        //$aResponse = $oVendor->{$oAction->filter}($oAction->options);
                        //Cache::put($strCacheKey, $aResponse, $nextTimeXur);
                        //return $aResponse;
                    }
                    else
                        return Cache::get($strCacheKey);
                }
            break;

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

                $aParams = [];
                if(isset($oAction->options->modes)) $aParams['modes'] = $oAction->options->modes;
                if(isset($oAction->options->groups)) $aParams['groups'] = $oAction->options->groups;

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
                                $this->destiny->getHistoricalStats($oCharacter->membershipType, $oCharacter->membershipId, $oCharacter->characterId, $aParams);
                            }
                        }
                        return $aCharacters;
                    }
                    else
                    {
                        foreach($aParameters['players'] AS $oPlayer)
                        {
                            $this->destiny->getHistoricalStats($oPlayer->membershipType, $oPlayer->membershipId, 0, $aParams);
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
                        $aRes[$aIds[0] .'-'. $aIds[1]][$aIds[2]] = $oStatsFilter->{$oAction->filter}($oAction->options->field);
                    }
                    return $aRes;
                }
            break;
        }
    }
}
?>