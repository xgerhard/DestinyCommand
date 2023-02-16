<?php

namespace App\Services\Destiny\DataTransferObjects;

class ProfileData extends BasicDataObject
{
    public static function fromArray(array $data): self
    {
        return new self(
            $data
        );
    }
}