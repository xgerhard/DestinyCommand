<?php

namespace App\Services\Destiny;

use Illuminate\Support\Facades\Http;
use App\Services\Destiny\DataTransferObjects\ProfileData;
use App\Services\Destiny\Requests\GetProfileRequest;
use App\Services\Destiny\Requests\GetHistoricalStatsRequest;

class DestinyService
{
    public function getProfile(string $membershipType, string $membershipId, array $components): ProfileData
    {
        $data = GetProfileRequest::build()
            ->withQuery(['components' => implode(',', $components)])
            ->withMembershipType($membershipType)
            ->withMembershipId($membershipId)
            ->send()
            ->throw()
            ->json('Response');

        return ProfileData::fromArray($data);
    }

    public function getHistoricalStats(string $membershipType, string $membershipId, string $characterId, array $parameteres): ProfileData
    {
        $data = GetHistoricalStatsRequest::build()
            ->withQuery($parameteres)
            ->withMembershipType($membershipType)
            ->withMembershipId($membershipId)
            ->withCharacterId($characterId)
            ->send()
            ->throw()
            ->json('Response');

        return ProfileData::fromArray($data);
    }
}