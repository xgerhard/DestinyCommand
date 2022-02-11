<?php
namespace App\Destiny;

use App\Destiny\Filters\InventoryFilter;
use App\Destiny\CharacterProfileValue;
use App\Destiny\CharacterProgressionValue;

class Profile
{
    public function __construct($aProperties = array())
    {
        foreach($aProperties AS $strProperty => $oProperty)
        {
            $this->$strProperty = $oProperty;
        }
    }

    function getCharacterProfileValue($oOptions)
    {   
        $aRes = [];
        if(isset($oOptions->field))
        {
            foreach($this->characters->data AS $iCharacterId => $oCharacter)
            {
                $aRes[$iCharacterId][$oOptions->field] = new CharacterProfileValue($oOptions->field, $oCharacter->{$oOptions->field}, $oCharacter->classHash);
            }
        }
        return $aRes;
    }

    function getCharacterProgression($oOptions)
    {
        $aRes = [];
        $iLatest = false;
        if($oOptions->latest) $iLatest = $this->getLatestCharacterId();

        foreach($this->characters->data AS $iCharacterId => $oCharacter)
        {
            if($iLatest && $iLatest != $iCharacterId) continue;

            foreach($oOptions->progressions as $iProgressionId)
            {
                if(isset($this->characterProgressions->data->{$iCharacterId}->progressions->{$iProgressionId}))
                {
                    $oProgression = $this->characterProgressions->data->{$iCharacterId}->progressions->{$iProgressionId};
                    $aRes[$iCharacterId][$iProgressionId] = new CharacterProgressionValue($iProgressionId, $oProgression->level, $oCharacter->classHash);
                }
            }
        }
        return $aRes;
    }

    function getCharacterEquipment($oOptions)
    {
        $aRes = [];
        $bPerks = $oOptions->perks ?? false;
        $iLatest = false;
        if($oOptions->latest) $iLatest = $this->getLatestCharacterId();

        foreach($this->characters->data AS $iCharacterId => $oCharacter)
        {
            if($iLatest && $iLatest != $iCharacterId) continue;

            $oInventoryFilter = new InventoryFilter(
                $this->characterEquipment->data->{$iCharacterId}->items,
                $this->itemComponents->instances->data,
                $this->itemComponents->sockets->data
            );
            $aRes[$iCharacterId] = $oInventoryFilter->getItems($oOptions->field, $bPerks);
        }
        return $aRes;
    }

    function getLatestCharacterId()
    {
        $dLastPlayed = false;
        $iLatestCharacterId = false;
        foreach($this->characters->data AS $iCharacterId => $oCharacter)
        {
            if($dLastPlayed === false || $dLastPlayed < strtotime($oCharacter->dateLastPlayed))
            {
                $dLastPlayed = strtotime($oCharacter->dateLastPlayed);
                $iLatestCharacterId = $iCharacterId;
            }
        }
        return $iLatestCharacterId;
    }
}
?>