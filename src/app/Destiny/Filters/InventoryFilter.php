<?php
namespace App\Destiny\Filters;

use App\Destiny\EquipmentItem;

class InventoryFilter
{
    private $items = [];
    private $instances;
    private $sockets;

    private $perks = false;

    public $primary     = 1498876634;
    public $secondary   = 2465295065;
    public $heavy       = 953998645;

    public $helmet      = 3448274439;
    public $gauntlet    = 3551918588;
    public $chest       = 14239492;
    public $legs        = 20886954;
    public $classitem   = 1585787867;

    public $ghost       = 4023194814;
    public $vehicle     = 2025709351;
    public $ship        = 284967655;

    public $subclass    = 3284755031;
    public $clan        = 4292445962;
    public $emblem      = 4274335291;
    public $emote       = 3054419239;
    public $aura        = 1269569095;

    public function __construct($aInventoryItems, $aInstances, $aSockets)
    {
        if(!empty($aInventoryItems))
        {
            foreach($aInventoryItems AS $oInventoryItem)
            {
                $this->items[$oInventoryItem->bucketHash] = $oInventoryItem;
            }
        }

        $this->instances = $aInstances;
        $this->sockets = $aSockets;
    }

    public function getItems($aSearchItems, $bPerks = false)
    {
        $bArray = true;
        if(!is_array($aSearchItems))
        {
            $aSearchItems = [$aSearchItems];
            $bArray = false;
        }

        $a = [];
        foreach($aSearchItems AS $strSearchItem)
        {
            $oItem = false;
            if(isset($this->items[$this->{$strSearchItem}]))
            {
                $oItem = new EquipmentItem($this->items[$this->{$strSearchItem}]);
                $oItem->load(
                    $this->instances->{$oItem->itemInstanceId},
                    $this->sockets->{$oItem->itemInstanceId}->sockets ?? array(),
                    $bPerks
                );
            }
            $a[$this->{$strSearchItem}] = $oItem;
        }
        return $bArray === false ? $a[$this->{$strSearchItem}] : $a;
    }
}
?>