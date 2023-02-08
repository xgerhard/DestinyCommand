<?php

namespace App\Services\Destiny\Requests;

use Illuminate\Http\Client\Factory as HttpFactory;
use JustSteveKing\Transporter\Request;

class DestinyRequest extends Request
{
    public function __construct(HttpFactory $http)
    {
        parent::__construct($http);

        parent::withHeaders([
            'X-API-Key' => config('services.destiny.api_key')
        ]);

        $this->baseUrl = config('services.destiny.uri');
    }
}