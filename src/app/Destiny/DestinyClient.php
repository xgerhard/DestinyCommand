<?php
namespace App\Destiny;

use App\RequestHandler;
use Exception;
use App\Destiny\DestinyRequest;
use Log;

class DestinyClient
{
    private $r;
    private $res = [];

    public function __construct()
    {
        $this->r = new RequestHandler;
    }

    public function searchDestinyPlayer($strGamertag)
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/SearchDestinyPlayer/-1/'. rawurlencode($strGamertag) .'/', [], 3400),
            'searchDestinyPlayer',
            $strGamertag
        );
    }

    public function searchUsers($strUser)
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/User/SearchUsers/', ['q' => $strUser], 0),
            'searchUsers',
            $strUser
        );
    }

    public function getProfile($iMembershipType, $iMembershipId, $aComponents = [])
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/'. $iMembershipType .'/Profile/'. $iMembershipId .'/', ['components' => implode(',', $aComponents)], 0),
            'getProfile',
            $iMembershipType .'-'. $iMembershipId
        );
    }

    public function getLinkedProfiles($iMembershipType, $iMembershipId)
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/'. $iMembershipType .'/Profile/'. $iMembershipId .'/LinkedProfiles/', [], 0),
            'getLinkedProfiles',
            $iMembershipType .'-'. $iMembershipId
        );
    }

    public function getDestinyManifest($strDatabase = false)
    {
        $this->r->addRequest(
            new DestinyRequest($strDatabase === false ? '/Platform/Destiny2/Manifest/' : $strDatabase),
            'getDestinyManifest',
            'getDestinyManifest'
        );
    }

    public function getHistoricalStats($iMembershipType, $iMembershipId, $iCharacterId, $aParams = [])
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/'. $iMembershipType .'/Account/'. $iMembershipId .'/Character/'. $iCharacterId .'/Stats/', $aParams),
            'getHistoricalStats',
            $iMembershipType .'-'. $iMembershipId .'-'. $iCharacterId
        );
    }

    public function getPublicVendors($aComponents = [])
    {
        $this->r->addRequest(
            new DestinyRequest('/Platform/Destiny2/Vendors/', ['components' => implode(',', $aComponents)], 0),
            'getPublicVendors',
            'getPublicVendors'
        );
    }

    public function get($strCategory)
    {
        if(!isset($this->res[$strCategory]))
        {
            $aResponses = $this->r->requester($strCategory);
            foreach($aResponses AS $strKey => $oResponse)
            {
                if(isset($oResponse->ErrorCode) && $oResponse->ErrorCode != 1) 
                {
                    throw new Exception($oResponse->Message);
                }else
                {
                    $this->res[$strCategory][$strKey] = $oResponse->Response;
                }
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