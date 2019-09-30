<?php
namespace App;

use App\Command\Query;

class Command
{
    public $user;
    public $userId = 0;
    public $channelId = 0;
    public $channel;
    public $query;
    public $token;
    public $platform;
    public $bot;
    public $defaultConsole;
    public $responseUser;

    public function __construct()
    {
        
    }

    public function setUser($strUser)
    {
        $this->user = $strUser;
        $this->responseUser = $strUser;
    }

    public function setResponseUser($strUser)
    {
        if(!is_null($strUser) && trim($strUser) != "")
        {
            $strUser = trim($strUser);
            if($strUser[0] == '@' && strlen($strUser) > 1) $strUser = substr($strUser, 1);
            $this->responseUser = $strUser;
        }
    }

    public function setUserId($iUserId)
    {
        $this->userId = $iUserId;
    }

    public function setToken($strToken)
    {
        $this->token = $strToken;
    }

    public function setChannel($strChannel)
    {
        $this->channel = $strChannel;
    }
	
	public function setChannelId($iChannelId)
    {
        $this->channelId = $iChannelId;
    }

    public function setQuery($strQuery)
    {
        if(is_null($strQuery)) $strQuery = 'default_info';
        $this->query = new Query($strQuery);
    }

    public function setBot($strBot)
    {
        if(is_null($strBot)) $strQuery = 'nightbot';
        $this->bot = strtolower($strBot);
    }

    public function setDefaultConsole($strConsole)
    {
        $aConsoles = array("xbox" => 1, "ps" => 2, "xb" => 1, "xb1" => 1, "psn" => 2, "playstation" => 2, "ps4" => 2, "pc" => 3, "bnet" => 3, "steam" => 3);
        $this->defaultConsole = $aConsoles[$strConsole] ?? 1;
    }

    public function setPlatform($strProvider)
    {
        $aProviders = array('youtube', 'twitch', 'discord', 'slack', 'json');
        if(!in_array(strtolower(trim($strProvider)), $aProviders)) $strProvider = 'twitch';
        $this->platform = strtolower(trim($strProvider));
    }
}
?>