<?php

namespace App;

use DB;
use App\UserPlayer;
use xgerhard\nbheaders\Nightbot;

class Setplayer
{
    private $user = null;

    public function __construct()
    {
        $oNB = new Nightbot(request());
        if($oNB->isNightbotRequest())
        {
            $oUser = $oNB->getUser();
            if($oUser)
                $this->user = (object) $oUser;
        }
    }

    public function getPlayer()
    {
        if($this->user)
        {
            $oUserPlayer = UserPlayer::where([['provider', '=', $this->user->provider], ['providerId', '=', $this->user->providerId]])->first();
            if($oUserPlayer)
                return $oUserPlayer;
        }
        return false;
    }

    public function setAccount($oBungieNetAccount)
    {
        if($this->user)
        {
            $oUserPlayer = $this->getPlayer();
            if($oUserPlayer)
            {
                if($oUserPlayer->BungieNetAccount->membershipId != $oBungieNetAccount->membershipId)
                {
                    $oUserPlayer->bungieNetAccountId = $oBungieNetAccount->id;
                    $oUserPlayer->save();
                }
            }
            else
            {
                $oUserPlayer = new UserPlayer;
                $oUserPlayer->provider = $this->user->provider;
                $oUserPlayer->providerId = $this->user->providerId;
                $oUserPlayer->bungieNetAccountId = $oBungieNetAccount->id;
                $oUserPlayer->save(); 
            }
            return $oUserPlayer;
        }
        return false;
    }

    public function setPlayer($oDestinyPlayer)
    {
        if($this->user)
        {
            $oUserPlayer = $this->getPlayer();
            if($oUserPlayer)
            {
                // Update player?
                if(!$oUserPlayer->destinyPlayer || $oUserPlayer->destinyPlayer->membership_id != $oDestinyPlayer->membershipId)
                {
                    $oUserPlayer->destinyPlayerId = $oDestinyPlayer->id;
                    $oUserPlayer->save();
                }
            }
            else
            {
                // New user player
                $oUserPlayer = new UserPlayer;
                $oUserPlayer->provider = $this->user->provider;
                $oUserPlayer->providerId = $this->user->providerId;
                $oUserPlayer->destinyPlayerId = $oDestinyPlayer->id;
                $oUserPlayer->save(); 
            }
            return $oUserPlayer;
        }
        return false;
    }
}