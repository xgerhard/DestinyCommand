<?php

namespace App\Services\Destiny\DataTransferObjects;

class HistoricalStatData extends BasicDataObject
{
    public static function fromArray(array $data): self
    {
        return new self(
            $data
        );
    }

    public function getStatId()
    {
        return $this->statId();
    }

    public function getValue()
    {
        return $this->basic->value ?? 0;
    }

    public function getDisplayValue()
    {
        return $this->basic->displayValue ?? 0;
    }

    public function getPgaValue()
    {
        return $this->pga->value ?? 0;
    }

    public function getPgaDisplayValue()
    {
        return $this->pga->displayValue ?? 0;
    }
}