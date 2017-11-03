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
                
                if(!empty($oPlayer->activities))
                {
                    $iGames = 0;
                    $iWin = 0;
                    $iLoss = 0;
                    $iKills = 0;
                    $iDeaths = 0;
                    $iAssists = 0;

                    foreach($oPlayer->activities AS $oActivity)
                    {
                        if($oActivity->standing != 3)
                        {
                            if($oActivity->standing == 0)
                                $iWin++;
                            elseif($oActivity->standing == 1)
                                $iLoss++;

                            $iGames++;
                            $iKills += $oActivity->kills;
                            $iDeaths += $oActivity->deaths;
                            $iAssists += $oActivity->assists;
                        }
                    }

                    $aTeam[] = new TrialsReportFireteamReport((object) array(
                        'displayName' => $oPlayer->displayName,
                        'kills' => $iKills,
                        'deaths' => $iDeaths,
                        'assists' => $iAssists,
                        'games' => $iGames,
                        'winp' => $iLoss === 0 ? 100 : ($iWin > 0 ? number_format((($iWin / $iGames) * 100), 2, ".", ",") : 0),
                        'kd' => $iDeaths > 0 ? number_format(($iKills / $iDeaths), 2, ".", ",") : $iKills,
                        'kda' => $iDeaths > 0 ? number_format((($iKills + $iAssists) / $iDeaths), 2, ".", ",") : $iKills
                    ));
                }
            }
            return $aTeam;
        }
        return false;
    }
}
?>