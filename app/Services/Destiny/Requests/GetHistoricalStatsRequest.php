<?php

namespace App\Services\Destiny\Requests;

use App\Services\Destiny\Requests\DestinyRequest;

class getHistoricalStatsRequest extends DestinyRequest
{
    protected string $method = 'GET';
    protected string $path = 'Destiny2/{membershipType}/Account/{membershipId}/Character/{characterId}/Stats/';

    public function withMembershipType(string $membershipType): self
    {
        return $this->setPath(str_replace('{membershipType}', $membershipType, $this->path()));
    }

    public function withMembershipId(string $membershipId): self
    {
        return $this->setPath(str_replace('{membershipId}', $membershipId, $this->path()));
    }

    public function withCharacterId(string $characterId): self
    {
        return $this->setPath(str_replace('{characterId}', $characterId, $this->path()));
    }
}