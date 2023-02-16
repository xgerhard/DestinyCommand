<?php

namespace App\Services\Destiny;

use App\Services\Destiny\DataTransferObjects\ProfileData;
use App\Services\Destiny\DataTransferObjects\HistoricalStatsData;
use App\Services\Destiny\Requests\GetProfileRequest;
use App\Services\Destiny\Requests\GetHistoricalStatsRequest;
use App\Services\Destiny\Exceptions\DestinyRequestException;
use Illuminate\Support\Collection;

class DestinyService
{
    public function getProfile(string $membershipType, string $membershipId, array $components): ProfileData
    {
        $data = GetProfileRequest::build()
            ->withQuery(['components' => implode(',', $components)])
            ->withMembershipType($membershipType)
            ->withMembershipId($membershipId)
            ->send()
            ->json();

        if (!isset($data['Response'])) {
            throw new DestinyRequestException($data);
        }

        return ProfileData::fromArray($data['Response']);
    }

    public function getHistoricalStats(string $membershipType, string $membershipId, string $characterId, array $parameteres): Collection
    {
        $data = GetHistoricalStatsRequest::build()
            ->withQuery($parameteres)
            ->withMembershipType($membershipType)
            ->withMembershipId($membershipId)
            ->withCharacterId($characterId)
            ->send()
            ->json();

        if (!isset($data['Response'])) {
            throw new DestinyRequestException($data);
        }

        return collect($data['Response'])->map(fn (array $data) => HistoricalStatsData::fromArray($data));
    }
}