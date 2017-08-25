<?php
namespace App;

class RequestHandler
{
    private $queue = array();

    public function add($strUrl, $aHeaders = array(), $xPost = false)
    {
        $this->queue[] = array(
            'url'       => $this->file_url($strUrl),
            'headers'   => $aHeaders,
            'post'      => $xPost
        );
    }

    public function run()
    {
        if(!empty($this->queue))
        {
            $curly = array();
            $result = array();
            $mh = curl_multi_init();

            // Add origin
            //$aHeaders[] = 'Origin: https://2g.be';

            foreach ($this->queue as $id => $d) 
            {
                $curly[$id] = curl_init();
                curl_setopt($curly[$id], CURLOPT_URL,               $d['url']);
                curl_setopt($curly[$id], CURLOPT_HEADER,            0);
                curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER,    1);
                curl_setopt($curly[$id], CURLOPT_USERAGENT,         'XgDestinyCommand - xgerhard@2g.be');
                curl_setopt($curly[$id], CURLOPT_TIMEOUT,           7);
                
                // Fix for localhost / disable on live
                curl_setopt($curly[$id], CURLOPT_SSL_VERIFYPEER,    0);

                // Post requests
                if(isset($d['post']) && $d['post'] !== false)
                {
                    $aHeaders[] = 'Content-Type:application/x-www-form-urlencoded';
                    curl_setopt($curly[$id], CURLOPT_POST, true);
                    curl_setopt($curly[$id], CURLOPT_POSTFIELDS, http_build_query ($d['post']));
                }
                else curl_setopt($curly[$id], CURLOPT_POST, false);

                // Additonal headers
                if(isset($d['headers']) && !empty($d['headers'])) $aHeaders = array_merge($aHeader, $d['headers']);

                // Add headers
                if(!empty($aHeaders)) curl_setopt($curly[$id], CURLOPT_HTTPHEADER, $aHeaders);

                curl_multi_add_handle($mh, $curly[$id]);
            }

            // execute the handles
            $running = null;
            do {
                curl_multi_exec($mh, $running);
            }
            while($running > 0);

            // get content and remove handles
            foreach($curly as $id => $c) 
            {
                $info = curl_multi_info_read($mh);
                if(isset($info['result']) AND $info['result'] == 28) throw new Exception('Time-out error, please try again later.');
                $result[$id] = curl_multi_getcontent($c);
                curl_multi_remove_handle($mh, $c);
            }
            curl_multi_close($mh);
            return $result;
        }
        return false;
    }

    public function file_url($strUrl)
    {
        $aParts = parse_url($strUrl);
        $aPathParts = array_map('rawurldecode', explode('/', $aParts['path']));
        $strScheme = !isset($aParts['scheme']) ? "//" : $aParts['scheme'] . '://';

        return
            $strScheme .
            $aParts['host'] .
            implode('/', array_map('rawurlencode', $aPathParts)) .
            (isset($aParts['query']) ? "?". $aParts['query'] : "")
        ;
    }
}
?>