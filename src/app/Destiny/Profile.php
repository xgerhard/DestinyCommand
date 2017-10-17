<?php
namespace App\Destiny;

use App\Destiny\Filters\InventoryFilter;

class Profile
{
    public function __construct($aProperties = array())
    {
        foreach($aProperties AS $strProperty => $oProperty)
        {
            $this->$strProperty = $oProperty;
        }
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