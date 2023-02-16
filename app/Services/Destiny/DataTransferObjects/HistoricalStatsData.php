<?php

namespace App\Services\Destiny\DataTransferObjects;

use App\Services\Destiny\DataTransferObjects\HistoricalStatData;

class HistoricalStatsData
{
    public function __construct($data)
    {
        foreach ($data as $periodType => $stats) {
            $this->$periodType = collect($stats)->map(fn (array $stats) => HistoricalStatData::fromArray($stats));
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data
        );
    }
}