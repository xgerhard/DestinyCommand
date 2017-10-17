<?php
namespace App\Destiny;

use App\Destiny\DestinyClient;
use GuzzleHttp\Client;

use ZipArchive;
use SQLite3;

class Manifest
{
    public function __construct()
    {
        $this->manifest_path = storage_path() .'/manifest/';
        $this->setting_file = $this->manifest_path . 'settings.json';
        $this->settings = $this->loadSettings();
    }

    public function check()
    {
        $oBungie = new DestinyClient;
        $oBungie->getDestinyManifest();

        $oCheck = $oBungie->get('getDestinyManifest')['getDestinyManifest'];

        if(isset($oCheck->mobileWorldContentPaths->en))
        {
            $strDatabase = $oCheck->mobileWorldContentPaths->en;
            if($this->getSetting('database') != $strDatabase)
            {
                // New database found.
                $aTables = $this->updateManifest($strDatabase);
                $this->setSetting('database', $strDatabase);
                $this->setSetting('tables', $aTables);
                return 'Manifest updated';
            }
            else return 'Manifest already up-to-date';
        }
        else return 'Bungie error, failed to update manifest';
    }

    private function updateManifest($strDatabase)
    {
        $oGuzzle = new Client([
            'http_errors' => false, 
            'verify' => false,
            'headers' => ['X-API-Key' => $_ENV['BUNGIE_API_KEY']]
        ]);

        $zData = $oGuzzle->get('https://bungie.net' . $strDatabase)->getBody();

        $strCachePath = $this->manifest_path .'cache/'. pathinfo($strDatabase, PATHINFO_BASENAME);
        if (!file_exists(dirname($strCachePath))) mkdir(dirname($strCachePath), 0777, true);
        file_put_contents($strCachePath.'.zip', $zData);

        $zZip = new ZipArchive();
        if ($zZip->open($strCachePath .'.zip') === TRUE) 
        {
            $zZip->extractTo($this->manifest_path .'cache');
            $zZip->close();
        }

        $aTables = array();
        if ($db = new SQLite3($strCachePath)) 
        {
            $oResult = $db->query("SELECT name FROM sqlite_master WHERE type='table'");
            while($aRow = $oResult->fetchArray()) 
            {
                $aTable = array();
                $oResult2 = $db->query("PRAGMA table_info(".$aRow['name'].")");
                while($aRow2 = $oResult2->fetchArray()) 
                {
                    $aTable[] = $aRow2[1];
                }
                $aTables[$aRow['name']] = $aTable;
            }
        }
        return $aTables;
    }

    public function loadSettings()
    {
        if (!file_exists($this->setting_file)) return (object) array();
        return json_decode(file_get_contents($this->setting_file));
    }

    public function setSetting($name, $value) 
    {
        $this->settings->{$name} = $value;
        file_put_contents($this->setting_file, json_encode($this->settings));
    }

    public function getSetting($name) 
    {
        if (isset($this->settings->{$name})) return $this->settings->{$name};
        return '';
    }

    public function queryManifest($strQuery) 
    {
        $strDatabase = $this->getSetting('database');
        $strCacheFilePath = $this->manifest_path .'cache/'. pathinfo($strDatabase, PATHINFO_BASENAME);

        $aResults = array();
        if ($db = new SQLite3($strCacheFilePath)) 
        {
            $oResult = $db->query($strQuery);
            while($aRow = $oResult->fetchArray()) 
            {
                $strKey = is_numeric($aRow[0]) ? sprintf('%u', $aRow[0] & 0xFFFFFFFF) : $aRow[0];
                $aResults[$strKey] = json_decode($aRow[1]);
            }
        }
        return $aResults;
    }

    public function browseDefinition($strTableName) 
    {
        return $this->queryManifest('SELECT * FROM '. $strTableName);
    }

    public function getDefinition($strTableName, $id)
    {
        $strTableName = 'Destiny'. $strTableName .'Definition';
        $aTables = $this->getSetting('tables');

        $strKey = $aTables->{$strTableName}[0];
        $strWhere = ' WHERE '. (is_numeric($id) ? $strKey .'='. $id .' OR '. $strKey .'='. ($id-4294967296) : $strKey .'="'. $id .'"');
        $aResults = $this->queryManifest('SELECT * FROM '. $strTableName . $strWhere);
        // Typecast to string since floats mess up index
        return isset($aResults[(string)$id]) ? $aResults[(string)$id] : false;
    }
}
?>