<?php
namespace App;

use Exception;
use Log;
use GuzzleHttp\Client;
use GuzzleHttp\Promise as GuzzlePromise;
use GuzzleHttp\Client\Exception\ConnectException;

class RequestHandler
{
    private $q = [];

    public function addRequest($oRequest, $strCategory, $strIdentifier)
    {
        $this->q[$strCategory][$strIdentifier] = $oRequest;
    }

    public function requester($xKey, $i = 0)
    {
        /*
        So this request function is a bit messy. Still not sure if its on my or Bungies end, but cURL is throwing ALOT of 'name lookup timed out' errors.
        Since we're working with a variety bots with different timeouts, we have to send a response in max 5-8 seconds.
        Temporary fix, have a low connect_timeout and just try to connect a few times. This reduced the timeout errors alot, however a few request will still fail after 5 attempts.
        */

        $oClient = new Client([
            'http_errors' => false, 
            'verify' => false,
            'timeout' => 6, // Response timeout
            'connect_timeout' => 1.5,
            'headers' => ['X-API-Key' => $_ENV['BUNGIE_API_KEY']],
            'force_ip_resolve' => 'v4'
        ]);

        $a = [];
        if(!empty($this->q))
        {
            foreach($this->q AS $strCategory => $aCategoryValue)
            {
                if($strCategory === $xKey)
                {
                    foreach($aCategoryValue AS $strIdentifier => $oRequest)
                    {
                        $a[$strIdentifier] = $oClient->requestAsync('GET', $oRequest->url);
                    }
                }
            }
        }

        $aReturn = [];
        if(!empty($a))
        {
            foreach(GuzzlePromise\settle($a)->wait() AS $strKey => $aResult)
            {
                if($aResult['state'] === 'fulfilled')
                {
                    $oResponse = $aResult['value'];
                    switch($oResponse->getStatusCode())
                    {
                        case 200:
                            $aReturn[$strKey] = json_decode($oResponse->getBody()->getContents());
                        break;

                        default: //case 503:
                            throw new Exception('Something went wrong, please try again later (1)');
                        break;
                    }
                }
                else
                {
                    if($aResult['reason'] instanceof \GuzzleHttp\Exception\ConnectException)
                    {
                        if($i<=5)
                        {
                            $i++;
                            $aHandlerContext = $aResult['reason']->getHandlerContext();
                            Log::debug($xKey .' - '. $i .' attempt - '. $aResult['reason']->getCode() .' - '. $aResult['reason']->getMessage() . (isset($aHandlerContext['url']) ? ' - '. $aHandlerContext['url'] : ""));
                            return $this->requester($xKey, $i);
                        }
                    }
                }
            }
        }
        return $aReturn;
    }
}
?>