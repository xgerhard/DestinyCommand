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
                    if($oSocket->isEnabled && $oSocket->isVisible)
                    {
                        $oPlug = $oManifest->getDefinition('InventoryItem', $oSocket->plugHash);

                        // Show progress if tracker is enabled
                        if(isset($oSocket->plugObjectives[0]) && $oSocket->plugObjectives[0]->visible)
                        {
                            $oObjective = $oManifest->getDefinition('Objective', $oSocket->plugObjectives[0]->objectiveHash);
                            if(isset($oObjective->progressDescription) && trim($oObjective->progressDescription) != "")
                            {
                                $oPlug->displayProperties->name .= ' ('. $oObjective->progressDescription .': '. $oSocket->plugObjectives[0]->progress .')';
                            }
                        }

                        // Show tier upgrade type
                        if(strpos($oPlug->displayProperties->name, 'Tier ') !== false && isset($oPlug->investmentStats[0]))
                        {
                            $oStat = $oManifest->getDefinition('Stat', $oPlug->investmentStats[0]->statTypeHash);
                            if(isset($oStat->displayProperties->name)) $oPlug->displayProperties->name = 'Tier '. $oPlug->investmentStats[0]->value .' ('. $oStat->displayProperties->name .')';
                        }

                        // Only show perks + mods
                        if($oPlug->inventory->bucketTypeHash == 1469714392 || $oPlug->inventory->bucketTypeHash == 3313201758 || $oPlug->inventory->bucketTypeHash == 2422292810)
                        {
                            $this->perks[] = $oPlug->displayProperties->name;
                        }
                    }
                }
            }
        }
    }
}
?>