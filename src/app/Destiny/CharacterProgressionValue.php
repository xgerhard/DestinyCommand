<?php
namespace App\Destiny;

class CharacterProgressionValue
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
            1062449239 => 'Current Trials card: Wins',
            2093709363 => 'Flawless'
        ];
        return $aTitles[$strKey] ?? '';
    }
}