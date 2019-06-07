<?php
namespace App\Providers;

use Cache;
use Carbon\Carbon;
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
                        $oVendor = new Vendor($oAction->options->hash, $oVendors);
                        $aVendorItems = $oVendor->{$oAction->filter}();
                        $oRefresh = Carbon::parse('next friday 17:00:00');
                        $aResponse = call_user_func_array('array_merge', $aVendorItems);

                        if(!empty($aResponse))
                        {
                            $aResponse['textStart'] = 'Xur is selling: ';
                            $aResponse['textEnd'] = 'Inventory reset: '. $oRefresh->format('M jS');

                            Cache::put($strCacheKey, $aResponse, $oRefresh);
                            Cache::forget('xur-location');
                            return $aResponse;
                        }
                    }
                    else
                    {
                        $aResponse = Cache::get($strCacheKey);
                        if(Cache::has('xur-location'))
                            $aResponse['textStart'] = 'Xur is located at: '. Cache::get('xur-location') .'. He is selling: ';

                        return $aResponse;
                    }
                }
            break;

            case 'profile':

                if($bPrepare === true)
                {
                    $aComponents = $oAction->options->params['components'] ?? array();
                    $aComponents = array_merge($aComponents, array(200)); // 200 is characters, we always need this.

                    foreach($aParameters['players'] as $oPlayer)
                    {
                        $this->destiny->getProfile($oPlayer->membershipType, $oPlayer->membershipId, $aComponents);
                    }
                }
                else
                {
                    $aProfiles = $this->destiny->get('getProfile');
                    foreach($aProfiles as $x => $aProfile)
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
                        foreach($aParameters['players'] as $oPlayer)
                        {
                            $this->destiny->getProfile($oPlayer->membershipType, $oPlayer->membershipId, array(200));
                        }
                        $aProfiles = $this->destiny->get('getProfile');
                        foreach($aProfiles as $oProfile)
                        {
                            foreach($oProfile->characters->data as $oCharacter)
                            {
                                $aCharacters[$oCharacter->characterId] = $oCharacter->classHash;
                                $this->destiny->getHistoricalStats($oCharacter->membershipType, $oCharacter->membershipId, $oCharacter->characterId, $aParams);
                            }
                        }
                        return $aCharacters;
                    }
                    else
                    {
                        foreach($aParameters['players'] as $oPlayer)
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
                        $aIds = explode('-', $x);
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