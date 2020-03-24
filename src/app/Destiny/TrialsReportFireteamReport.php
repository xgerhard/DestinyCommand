<?php
namespace App\Destiny;

class TrialsReportFireteamReport
{
    public $displayName;
    public $kills;
    public $deaths;
    public $assists;
    public $winp;
    public $kd;
    public $kda;
    public $games;
    public $flawless;

    public function __construct($oStatReport)
    {
        $this->displayName = $oStatReport->displayName;
        $this->kills = $oStatReport->kills;
        $this->deaths = $oStatReport->deaths;
        $this->assists = $oStatReport->assists;
        $this->winp = $oStatReport->winp;
        $this->kd = $oStatReport->kd;
        $this->kda = $oStatReport->kda;
        $this->games = $oStatReport->games;
        $this->flawless = $oStatReport->flawless;
    }
}
?>