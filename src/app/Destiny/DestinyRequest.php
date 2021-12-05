<?php
namespace App\Destiny;

class DestinyRequest
{
    public $url;
    public $cache;
    public $params;
    public $baseUrl = "https://www.bungie.net";
    public $method;
    public $postFields;

    public function __construct($strUrl, $aParams = [], $iCache = 0, $strMethod = 'GET', $aPostFields = [])
    {
        $this->url = $this->baseUrl . $strUrl;
        $this->cache = $iCache;
        $this->params = $aParams;
        $this->method = $strMethod;
        if(!empty($aParams)) $this->url .= '?' . http_build_query($aParams);
        if(!empty($aPostFields)) $this->postFields = $aPostFields;
    }
}
?>