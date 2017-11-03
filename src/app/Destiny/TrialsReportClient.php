<?php
namespace App\Destiny;

use App\Destiny\TrialsReportRequest;

use App\RequestHandler;
use Exception;
use Log;

class TrialsReportClient
{
    private $r;
    private $res = [];
    private $base_url = '';
    public function __construct()
    {
        $this->r = new RequestHandler;
    }

    public function getFireteam($iMembershipId, $iMembershipType)
    {
        $this->r->addRequest(
            new TrialsReportRequest('/player/'. $iMembershipId .'/fireteam'),
            'getFireteam',
            $iMembershipType .'-'. $iMembershipId
        );
    }

    public function get($strCategory)
    {
        if(!isset($this->res[$strCategory]))
        {
            $aResponses = $this->r->requester($strCategory);
            foreach($aResponses AS $strKey => $oResponse)
            {
                $this->res[$strCategory][$strKey] = $oResponse;
            }
        }
        if(!isset($this->res[$strCategory]))
        {
            Log::debug($strCategory .' - failed');
            throw new Exception('Something went wrong, please try again later (2)');
        }
        return $this->res[$strCategory];
    }
}
?>