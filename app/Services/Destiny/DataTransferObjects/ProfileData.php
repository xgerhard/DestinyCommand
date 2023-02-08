<?php

namespace App\Services\Destiny\DataTransferObjects;

class ProfileData
{
    public function __construct($data)
    {
        foreach($data as $key => $value)
        {
            $this->$key = $value;
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data
        );
    }
}