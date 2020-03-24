<?php
namespace App\Destiny\Filters;

use App\Destiny\TrialsReportFireteamReport;

class TrialsReportFilter
{
    public function __construct($oData)
    {
        if(!empty($oData))
        {
            foreach($oData AS $strKey => $xValue)
            {
                $this->{$strKey} = $xValue;
            }
        }
    }

    public function getFireteamStats($aOptions = [])
    {
        if(isset($this->results) && !empty($this->results))
        {
            $aTeam = [];
            foreach($this->results AS $oPlayer)
            {
                if(isset($oPlayer->current))
                {
                    $aTeam[] = new TrialsReportFireteamReport((object) array(
                        'displayName' => $oPlayer->displayName,
                        'kills' => $oPlayer->current->kills,
                        'deaths' => $oPlayer->current->deaths,
                        'assists' => $oPlayer->current->assists,
                        'games' => $oPlayer->current->matches,
                        'winp' => $oPlayer->current->losses == 0 ? 100 : (($oPlayer->current->matches - $oPlayer->current->losses) > 0 ? number_format(((($oPlayer->current->matches - $oPlayer->current->losses) / $oPlayer->current->matches) * 100), 2, ".", ",") : 0),
                        'kd' => $oPlayer->current->deaths > 0 ? number_format(($oPlayer->current->kills / $oPlayer->current->deaths), 2, ".", ",") : $oPlayer->current->kills,
                        'kda' => $oPlayer->current->deaths > 0 ? number_format((($oPlayer->current->kills + $oPlayer->current->assists) / $oPlayer->current->deaths), 2, ".", ",") : $oPlayer->current->kills,
                        'flawless' => $oPlayer->current->flawless
                    ));
                }
            }
            return $aTeam;
        }
        return array(false);
    }
}
?>