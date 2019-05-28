<?php

namespace App\Destiny;

use App\Destiny\Equipmentitem;

class Vendor
{
    public $refreshDate;
    private $saleItems;

    public function __construct($iHash, $oData)
    {
        if($oData)
        {
            if(isset($oData->vendors->data->{$iHash}->nextRefreshDate))
                $this->refreshDate = $oData->vendors->data->{$iHash}->nextRefreshDate;

            if(isset($oData->sales->data->{$iHash}->saleItems))
                $this->saleItems = $oData->sales->data->{$iHash}->saleItems;
        }
    }

    public function getSales($strFilter = false)
    {
        $aWeapons = [];
        $aHelmets = [];
        $aGauntlets = [];
        $aChests = [];
        $aLegs = [];
        $aConsumables = [];

        foreach($this->saleItems as $oSaleItem)
        {
            $oItem = new EquipmentItem($oSaleItem);
            $oItem->load($oSaleItem, [], false);

            if($oItem)
            {
                switch($oItem->bucketTypeHash)
                {
                    // Consumables
                    case 1469714392:
                        $aConsumables[] = $oItem;
                    break;

                    // Weapons
                    case 1498876634:
                    case 2465295065:
                    case 953998645:
                        $aWeapons[] = $oItem;
                    break;

                    // Gear
                    case 3448274439:
                        $aHelmets[] = $oItem;
                    break;
                    case 3551918588:
                        $aGauntlets[] = $oItem;
                    break;
                    case 14239492:
                        $aChests[] = $oItem;
                    break;
                    case 20886954:
                        $aLegs[] = $oItem;
                    break;
                }
            }
        }

        $aReturn = [
            'weapons' => $aWeapons,
            'helmets' => $aHelmets,
            'gauntlets' => $aGauntlets,
            'chests' => $aChests,
            'legs' => $aLegs,
            'consumables' => $aConsumables
        ];
        return $strFilter ? $aReturn[$strFilter] : $aReturn;
    }
}