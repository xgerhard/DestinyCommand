<?php

namespace App\Services\Destiny\DataTransferObjects;

class BasicDataObject
{
    public function __construct($data)
    {
        $data = json_decode(json_encode($data));
        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }
    }
}