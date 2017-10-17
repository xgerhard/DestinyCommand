<?php
namespace App\Destiny;

use App\Destiny\Manifest;

class EquipmentItem
{
    public function __construct($oEquipmentItem)
    {
        $this->itemInstanceId = $oEquipmentItem->itemInstanceId;
        $this->itemHash = $oEquipmentItem->itemHash;
    }

    public function load($oItemInstance, $aSockets = array(), $bPerks)
    {
        $oManifest = new Manifest;
        $oItem = $oManifest->getDefinition('InventoryItem', $this->itemHash);

        $this->name = $oItem->displayProperties->name;
        $this->light = $oItemInstance->primaryStat->value ?? 0;

        if($bPerks && !$oItem->redacted)
        {
            if(!empty($aSockets))
            {
                foreach($aSockets AS $oSocket)
                {
                    if($oSocket->isEnabled != false)
                    {
                        $oPlug = $oManifest->getDefinition('InventoryItem', $oSocket->plugHash);
                        if($oPlug->inventory->bucketTypeHash == 1469714392 || $oPlug->inventory->bucketTypeHash == 3313201758) $this->perks[] = $oPlug->displayProperties->name; // Only show perks + mod
                    }
                }
            }
        }
    }
}
?>