<?php
namespace App\Destiny;

class CharacterProfileValue
{
    public $displayValue;
    public $title;
    public $classHash;

    public function __construct($strKey, $xValue, $strClassHash)
    {
        $this->displayValue = $xValue;
        $this->title = $this->getTitle($strKey);
        $this->classHash = $strClassHash;
    }

    private function getTitle($strKey)
    {
        $aTitles = [
            "light" => "Power level"
        ];
        return $aTitles[$strKey] ?? "";
    }
}