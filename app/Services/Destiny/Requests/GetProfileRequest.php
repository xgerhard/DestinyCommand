<?php

namespace App\Services\Destiny\Requests;

use App\Services\Destiny\Requests\DestinyRequest;

class getProfileRequest extends DestinyRequest
{
    protected string $method = 'GET';
    protected string $path = 'Destiny2/{membershipType}/Profile/{membershipId}/';

    public function withMembershipType(string $membershipType): self
    {
        return $this->setPath(str_replace('{membershipType}', $membershipType, $this->path()));
    }

    public function withMembershipId(string $membershipId): self
    {
        return $this->setPath(str_replace('{membershipId}', $membershipId, $this->path()));
    }
}