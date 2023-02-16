<?php

namespace App\Services\Destiny\Exceptions;

use Exception;

class DestinyRequestException extends Exception
{
    public $message;

    public function __construct($data)
    {
        if (isset($data['Message'])) {
            $this->message = $data['Message'];
        }
    }
}