<?php
namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;

class BungieNameConverterController
{
    public function convert(Request $request)
    {
        $bSubmit = $request->isMethod('post');
        $aErrors = [];
        $strBungieName = $request->input('username', '');
        $strUrl = $request->input('url', '');
        $strNewUrl = false;

        if(substr($strUrl, 0, 4 ) != 'http')
        {
            $aErrors[] = 'Url is not a valid url';
        }

        if(strpos($strBungieName, '#') === false)
        {
            $aErrors[] = 'Not a valid Bungie name';
        }

        if(empty($aErrors))
        {
            $aUrl = parse_url($strUrl);
            $strNewUrl = $aUrl['scheme'] .'://'. $aUrl['host'] . $aUrl['path'];

            if(isset($aUrl['query']))
            {
                parse_str($aUrl['query'], $aQuerystring);

                if(isset($aQuerystring['default_console']))
                    unset($aQuerystring['default_console']);

                if(isset($aQuerystring['query']))
                {
                    $aQuery = explode(' ', $aQuerystring['query']);
                    $aQuerystring['query'] = $aQuery[0] . rawurlencode(' '. $strBungieName);
                }

                $aNewQuerystring = [];
                foreach($aQuerystring as $param => $value){
                    $aNewQuerystring[] = $param .'='. $value;
                }

                $strNewUrl .= '?'. implode('&', $aNewQuerystring);
            }
        }

        return view('BungieNameConverter', [
            'errors' => $aErrors,
            'username' => $strBungieName,
            'url' => $strUrl,
            'newUrl' => $strNewUrl,
            'submit' => $bSubmit
        ]);
    }

    private function isPlatform($str)
    {
        $aPlatforms = ['xbox', 'ps', 'pc', 'steam'];
        return in_array(strtolower($str, $aPlatforms));
    }
}