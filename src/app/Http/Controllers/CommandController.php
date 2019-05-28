<?php
namespace App\Http\Controllers;

use App\OAuth\OAuthHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Command;
use App\Setplayer;

use App\Destiny\DestinyClient;
use App\Destiny\Filters\InventoryFilter;
use App\Destiny\Manifest;

use App\Destiny\EquipmentItem;
use App\Destiny\Stat;
use App\Destiny\TrialsReportFireteamReport;
use App\Destiny\DestinyPlayer;
use App\Destiny\BungieNetAccount;
use App\Destiny\CharacterProfileValue;

use App\Providers\BungieProvider;

use Exception;

class CommandController
{
    private $command;

    public function parseRequest(Request $request)
    {
        try
        {
            $oCommand = new Command;

            // Channel.
            // For a Nightbot request this header should always be set, set provider based on this.
            if($request->header('Nightbot-Channel'))
            {
                parse_str($request->header('Nightbot-Channel'), $aNightBotChannel);
                $oCommand->setChannel($aNightBotChannel['displayName']);
                //$oCommand->setChannelId($aNightBotChannel['displayName']);
                $oCommand->setPlatform($aNightBotChannel['provider']);
                $oCommand->setBot('nightbot');
            }
            else
            {
                // Provider (Default to Twitch)
                $oCommand->setPlatform(Input::get('platform', 'twitch'));

                // Bot
                $oCommand->setBot(Input::get('bot', 'nightbot'));

                // Channel
                if(Input::has('channel'))
                {
                    $oCommand->setChannel(Input::get('channel'));
                }
            }

            // User (Default to System)
            if($request->header('Nightbot-User'))
            {
                parse_str($request->header('Nightbot-User'), $aNightBotUser);
                $oCommand->setUser($aNightBotUser['displayName']);
                $oCommand->setUserId($aNightBotUser['providerId']);
                $oCommand->setPlatform($aNightBotUser['provider']);
            }
            else
            {
                $oCommand->setUser(Input::get('user', 'System'));
            }

            // Token
            if(Input::has('token'))
            {
                $oCommand->setToken(Input::get('token'));
            }

            // Dont display username
            $bUsername = true;
            if(Input::has('nousername')) $bUsername = false;

            // Dont display gamertag
            $bGamertag = true;
            if(Input::has('nogamertag')) $bGamertag = false;

            $oCommand->setDefaultConsole(Input::get('default_console', 'xbox'));
            $oCommand->setResponseUser(Input::get('response_user', ''));

            // Query (Default to default_info)
            $oCommand->setQuery(Input::get('query'));
            $this->command = $oCommand;

            // Run the command
            $aRes = $this->runCommand();
            if($this->command->platform == 'json')
            {
                header('Content-type:application/json;charset=utf-8');
                echo json_encode($aRes);
                die;
            }

            // Format Discord
            if($this->command->platform == 'discord' && isset($this->command->responseUser)) $this->command->responseUser = $this->formatDiscord($this->command->responseUser);
            
            // Leading @ to tag users in Twitch chat.
            $strRes = $bUsername ? '@' .$this->command->responseUser .': ' : '';

            // This shouldnt be here.
            $aClasses = array(
                671679327   => 'Hunter',
                2271682572  => 'Warlock',
                3655393761  => 'Titan'
            );

            // We need seperate handlers that format the output for different platforms, for now just use the Discord code blocks.
            if($this->command->platform == 'discord') $strRes .= '```';

            // Loop results
            if(isset($aRes['players']) && !empty($aRes['players']))
            {
                foreach($aRes['players'] AS $oPlayer)
                {
                    // The membershiptype + id will be the identifier of the player in the response array.
                    $strKey = $oPlayer->membershipType .'-'. $oPlayer->membershipId;
                    if(isset($aRes['response']) && !empty($aRes['response']))
                    {
                        if(isset($aRes['response'][$strKey]))
                        {
                            $bPlaylistIntro = false;
                            $strRes .= $bGamertag ? (($oPlayer->membershipType == 4 ? $this->formatNoBnet($oPlayer->displayName) : $oPlayer->displayName) .': ') : '';
                            if(count($aRes['response'][$strKey]) > 1) $strRes .= '[';
                            $bFound = false;

                            // Loop characters of the player, could be 1 character with Id 0 which will the overall account stat.
                            foreach($aRes['response'][$strKey] AS $iCharacterId => $aCharacter)
                            {
                                $strCharacterRes = false;
                                foreach($aCharacter AS $x)
                                {
                                    switch(true)
                                    {
                                        case $x instanceof EquipmentItem:

                                            $oCharacterItem = $x;
                                            $strCharacterRes .= $oCharacterItem->name;
                                            if(isset($oCharacterItem->light) && $oCharacterItem->light > 50) $strCharacterRes .= ' ['. $oCharacterItem->light .']';
                                            if(isset($oCharacterItem->perks) && !empty($oCharacterItem->perks))
                                            {
                                                $strCharacterRes .= ' ['. implode(", ", $oCharacterItem->perks) .']';
                                            }
                                            $strCharacterRes .= ", ";
                                            $bFound = true;
                                        break;

                                        case $x instanceof Stat:
                                            $oStat = $x;
                                            $strCharacterRes .= $oStat->title .': '. $oStat->displayValue;
                                            $strCharacterRes .= ", ";
                                            $bFound = true;
                                        break;

                                        case $x instanceof CharacterProfileValue:
                                            $oCharacterProfile = $x;
                                            $strCharacterRes .= $aClasses[$oCharacterProfile->classHash] .': '. $oCharacterProfile->title .': '. $oCharacterProfile->displayValue;
                                            $strCharacterRes .= ", ";
                                            $bFound = true;
                                        break;

                                        case $x instanceof TrialsReportFireteamReport:
                                            $oFireteamStatReport = $x;
                                            $strCharacterRes .= '['. $this->formatNoBnet($oFireteamStatReport->displayName) .':  Games: '. $oFireteamStatReport->games .' (W'. $oFireteamStatReport->winp .'%) | KD: '. $oFireteamStatReport->kd .' | KA/D: '. $oFireteamStatReport->kda .'], ';
                                            $bFound = true;
                                        break;

                                        default:
                                            $bFound = true;
                                            $strCharacterRes .= 'No stats found, ';
                                    }
                                }

                                if($strCharacterRes !== false)
                                {
                                    if(isset($oStat) && $bPlaylistIntro === false)
                                    {
                                        if(substr($oStat->playlist, 0, 3) == 'all')
                                            $oStat->playlist = substr($oStat->playlist, 3);
                                        elseif($oStat->playlist == 'pvecomp_gambit')
                                            $oStat->playlist = 'Gambit';
                                        elseif($oStat->playlist == 'pvecomp_mamba')
                                            $oStat->playlist = 'Gambit Prime';

                                        $strRes .= '['. ucfirst($oStat->playlist) .'] ';
                                        $bPlaylistIntro = true;
                                    }
                                    if($iCharacterId != 0 && isset($this->prep[$iCharacterId])) $strRes .= $aClasses[$this->prep[$iCharacterId]] .': ';
                                    $strRes .= $strCharacterRes;
                                }
                            }
                            $strRes = ($bFound === true ? substr($strRes, 0, -2) : $strRes) . (count($aRes['response'][$strKey]) > 1 ? ']' : '') .', ';
                        }
                        $strRes = substr($strRes, 0, -2) .', ';
                    }
                }
                $strRes = substr($strRes, 0, -2);
            }

            elseif(!isset($aRes['response']['text']) && !empty($aRes['response']) && is_array($aRes['response']) && !empty($aRes['response']))
            {
                if(isset($aRes['response']['textStart']))
                    $strRes .= $aRes['response']['textStart'];

                foreach($aRes['response'] as $x)
                {
                    switch(true)
                    {
                        case $x instanceof EquipmentItem:
                            // Name, light/power, perks
                            $strRes .= $x->name;
                            if(isset($x->light) && $x->light > 50)
                                $strRes .= ' ['. $x->light .']';
                            if(isset($x->perks) && !empty($x->perks))
                                $strRes .= ' ['. implode(", ", $x->perks) .']';

                            // Item costs for vendors
                            if(isset($x->costs) && !empty($x->costs))
                            {
                                $strRes .= ' [';
                                foreach($x->costs as $oCost)
                                {
                                    $strRes .= $oCost->quantity .', ';
                                }
                                $strRes = substr($strRes, 0, -2). ']';
                            }
                            $strRes .= ', ';
                        break;
                    }
                }
                $strRes = substr($strRes, 0, -2);

                if(isset($aRes['response']['textEnd']))
                    $strRes .= ' ['. $aRes['response']['textEnd'] .']';
           }

            if(isset($aRes['response']['text']))
                $strRes .= ' '. implode(",", $aRes['response']['text']);

            $strRes .= '.';
            if($this->command->platform == 'discord') $strRes .= '```';
            return $strRes;
        }
        catch (Exception $e)
        {
            return '@'. ($this->command->responseUser ?? 'System') .': '. $e->getMessage() .'.';
        }
    }

    private function MergeArrays($Arr1, $Arr2)
    {
        foreach($Arr2 as $key => $Value)
        {
            if(array_key_exists($key, $Arr1) && is_array($Value))
                $Arr1[$key] = $this->MergeArrays($Arr1[$key], $Arr2[$key]);
            else
                $Arr1[$key] = $Value; 
        }
        return $Arr1;
    }

    private function formatDiscord($strName)
    {
        $aDiscordFormats = array("_", "*", "+");
        $aDiscordReplace = array("\_", "\*", " ");
        return str_replace($aDiscordFormats, $aDiscordReplace, $strName);
    }

    private function runCommand()
    {
        $aPlayers = [];
        $aResponse = [];

        // Load Player info if Command requires Player info.
        if($this->command->query->reqUser === true)
        {
            $aPlayers = $this->getPlayers();
            if(empty($aPlayers)) 
            {
                $oSetplayer = new Setplayer;
                $oUserPlayer = $oSetplayer->getPlayer();
                if($oUserPlayer)
                    $aPlayers[$oUserPlayer->destinyPlayer->displayName] = $oUserPlayer->destinyPlayer;
                else
                    throw new Exception('No players found');
            }
        }

        // Since were using multiCurl were looping the actions twice, first time to setup the needed request, so we can perform them all at once. Second loop we can read the responses.
        foreach($this->command->query->actions AS $oAction)
        {
            if($oAction->provider == 'plain_text') continue;
            $strClass = 'App\Providers\\'. $oAction->provider;
            if(!isset(${$oAction->provider})) ${$oAction->provider} = (new $strClass);
            $this->prep = ${$oAction->provider}->fetch($oAction, array('players' => $aPlayers), true);
        }

        foreach($this->command->query->actions AS $oAction)
        {
            // Handle setplayer feature here
            if($oAction->key == 'setplayer')
            {
                $aPlayers = $this->getPlayers();
                if(empty($aPlayers))
                    $oAction->text = 'No player info provided';
                else
                {
                    // Loop once to get the first player
                    foreach($aPlayers AS $oPlayer){ break; }
                    $aConsoles = [1 => "Xbox", 2 => "PS", 4 => "PC"];
                    $oSetplayer = new Setplayer;

                    if($oSetplayer->setPlayer($oPlayer))
                        $oAction->text = 'Succesfully saved player: '. $oPlayer->displayName .' ['. $aConsoles[$oPlayer->membershipType] .']';
                    else
                        $oAction->text = 'Something went wrong saving player: '. $oPlayer->displayName .' ['. $aConsoles[$oPlayer->membershipType] .']. Note: setplayer is a Nightbot only feature';
                }
            }

            $x = $oAction->provider == 'plain_text' ? ['text' => [$oAction->text]] : ${$oAction->provider}->fetch($oAction, array('players' => $aPlayers), false);
            if(is_array($x))
                $aResponse = $this->MergeArrays($aResponse, $x);
            else
                $aResponse[$oAction->key] = $x;
        }
        return array(
            'players' => $aPlayers,
            'response' => $aResponse
        );
    }

    public function getPlayers()
    {
        $aPlayers = [];
        if(!empty($this->command->query->gamertags))
        {
            $oBungie = new DestinyClient;
            $aTempPlayers = [];

            foreach($this->command->query->gamertags AS $i => $strGamertag)
            {
                if(trim($strGamertag) == "")
                {
                    unset($this->command->query->gamertags[$i]);
                    continue;
                }
 
                // Since platform parameter is not required, we have to set a default for if its not given
                if(isset($this->command->query->consoles[$i]))
                    $iTempConsole = $this->command->query->consoles[$i];
                elseif(empty($this->command->query->consoles))
                    $iTempConsole = $this->command->defaultConsole;
                else
                    $iTempConsole = reset($this->command->query->consoles);

                // Pc lookup but no hastag found, lets replace - with #
                if($iTempConsole == 4 && strpos($strGamertag, '#') === false)
                {
                    $strGamertag = $this->formatToHashtag($strGamertag);
                    $this->command->query->gamertags[$i] = $strGamertag;
                }

                // Setup playersearch
                $oBungie->SearchDestinyPlayer($strGamertag);
                
                // Save temp player array so we can filter these in the player search responses
                $aTempPlayers[$strGamertag] = $iTempConsole;
            }
            // Get playersearch responses
            $aPlayersResults = $oBungie->get('searchDestinyPlayer');

            // Loop player responses
            foreach($aPlayersResults AS $strGamertag => $aFoundPlayers)
            {
                if(empty($aFoundPlayers))
                {
                    if($aTempPlayers[$strGamertag] == 4 || strpos($strGamertag, '#') !== false) $strGamertag = $this->formatNoBnet($strGamertag);
                    throw new Exception('Player '. $strGamertag .' not found');
                }
                else
                {
                    // Loop players in the response and filter out the right platform
                    $aPlayersTemp = [];
                    foreach($aFoundPlayers AS $oFoundPlayer)
                    {
                        // Save players for faster future searches
                        if($oDestinyPlayer = DestinyPlayer::where([['membershipId', '=', $oFoundPlayer->membershipId], ['membershipType', '=', $oFoundPlayer->membershipType]])->first())
                        {
                           if(strtolower($oDestinyPlayer->displayName) != strtolower($oFoundPlayer->displayName))
                           {
                               $oDestinyPlayer->displayName = $oFoundPlayer->displayName;
                               $oDestinyPlayer->save();
                           }
                        }
                        else
                        {
                            $oDestinyPlayer = new DestinyPlayer;
                            $oDestinyPlayer->membershipId = $oFoundPlayer->membershipId;
                            $oDestinyPlayer->membershipType = $oFoundPlayer->membershipType;
                            $oDestinyPlayer->displayName = $oFoundPlayer->displayName;
                            $oDestinyPlayer->save();
                        }

                        if(strtolower($oDestinyPlayer->displayName) == strtolower($strGamertag) && ($oDestinyPlayer->membershipType == $aTempPlayers[$strGamertag] || count($aFoundPlayers) == 1))
                        {
                            $aPlayersTemp[] = $oDestinyPlayer;
                        }
                    }

                    // Jet the pirate will thank me later, thanks Vlad ;) 
                    if(!empty($aPlayersTemp))
                    {
                        $aPlayers[$strGamertag] = end($aPlayersTemp);
                    }
                }
            }
        }
        elseif(Input::has('membershipId') && Input::has('membershipType') && Input::has('displayName'))
        {
            $aPlayers[Input::get('displayName')] = (object) array(
                'membershipId' => Input::get('membershipId'),
                'membershipType' => Input::get('membershipType'),
                'displayName' => Input::get('displayName')
            );
        }
        return $aPlayers;
    }

    private function formatToHashtag($strGamertag)
    {
        $iPos = strrpos($strGamertag, "-");
        if($iPos !== false) $strGamertag = substr_replace($strGamertag, '#', $iPos, 1);
        return $strGamertag;
    }
    
    private function formatNoBnet($strGamertag)
    {
        $aGamertag = explode("#", $strGamertag);
        if(count($aGamertag) > 1) $strGamertag =  str_replace("#". end($aGamertag), "", $strGamertag);
        return $strGamertag;
    }

    private function returnerino($res)
    {
        return $res . '<hr>Load time: '. (microtime(true) - LARAVEL_START);
    }
}
?>