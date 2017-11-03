<?php
namespace App\Destiny;

class TrialsReportRequest
{
    public $url;
    public $cache;
    public $params;
    public $baseUrl = "https://api.trialsofthenine.com";

    public function __construct($strUrl, $aParams = [], $iCache = 0)
    {
        $this->url = $this->baseUrl . $strUrl;
        $this->cache = $iCache;
        $this->params = $aParams;
        if(!empty($aParams)) $this->url .= '?' . http_build_query($aParams);
    }
}
?>