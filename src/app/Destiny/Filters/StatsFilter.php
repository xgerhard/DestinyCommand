<?php
namespace App\Destiny\Filters;

use App\Destiny\Stat;

class StatsFilter
{
    private $playlist;
    private $timePeriod;
    private $stats = [];

    public function __construct($oStats)
    {
        foreach($oStats AS $strPlaylist => $oPlaylist);

        if(empty((array)$oPlaylist)) return false;
        $this->playlist = $strPlaylist;

        foreach($oPlaylist AS $strTimePeriod => $oStatsObject);
        $this->timePeriod = $strTimePeriod;

        foreach($oStatsObject AS $strKey => $oStat)
        {
            $this->stats[$strKey] = $oStat;
        }
    }

    public function getStats($aSearchItems)
    {
        $bArray = true;
        if(!is_array($aSearchItems))
        {
            $aSearchItems = [$aSearchItems];
            $bArray = false;
        }

        $a = [];
        foreach($aSearchItems AS $strSearchItem)
        {
            $oStat = false;
            if(isset($this->stats[$strSearchItem]))
            {
                $oStat = new Stat($this->stats[$strSearchItem], $this->playlist);
            }
            $a[$strSearchItem] = $oStat;
        }
        return $bArray === false ? $a[$strSearchItem] : $a;
    }
}
?>