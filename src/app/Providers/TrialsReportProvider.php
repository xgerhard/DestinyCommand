<?php
namespace App\Providers;

use App\Destiny\TrialsReportClient;
use App\Destiny\Filters\TrialsReportFilter;

class TrialsReportProvider
{
	public function __construct()
	{
		$this->tr = new TrialsReportClient;
	}
    
    function fetch($oAction, $aParameters, $bPrepare = false)
	{
		switch($oAction->endpoint)
		{
			case 'getFireteam':

				if($bPrepare === true)
				{
					foreach($aParameters['players'] AS $oPlayer)
					{
						$this->tr->getFireteam($oPlayer->membershipId, $oPlayer->membershipType);
					}
                }
                else
                {
                    $aTRProfiles = $this->tr->get('getFireteam');
					foreach($aTRProfiles AS $x => $aTRProfile)
					{
						$oFilter = new TrialsReportFilter($aTRProfile);
						$aProfiles[$x][0] = $oFilter->{$oAction->filter}($oAction->options ?? []);
					}
					return $aProfiles;
                }
            break;
        }
    }
}
?>