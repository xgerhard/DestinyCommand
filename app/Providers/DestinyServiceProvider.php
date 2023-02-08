<?php

namespace App\Providers;

use App\Services\Destiny\DestinyService;
use App\Services\Destiny\Requests\DestinyRequest;
use App\Services\Destiny\Request\GetProfileRequest;

use Illuminate\Support\ServiceProvider;

class DestinyServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(
            GetProfileRequest::class,
            fn() => new GetProfileRequest(
                config('services.destiny.api_key'),
                config('services.destiny.uri')
            )
        );
    }
}