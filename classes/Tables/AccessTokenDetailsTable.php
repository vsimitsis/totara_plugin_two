<?php

namespace ThirstPlugin\Tables;

use ThirstPlugin\Model\AccessToken;
use ThirstPlugin\Model\User;

class AccessTokenDetailsTable extends BaseTable implements TableInterface
{
    /** @var AccessToken $accessToken */
    private $accessToken;
    /** @var User $addedBy */
    private $addedBy;

    public static function getUniqueId(): string
    {
        return 'access-token-details';
    }

    public function setAccessToken(AccessToken $accessToken): self
    {
        $this->accessToken = $accessToken;
        return $this;
    }

    public function setAddedBy(User $addedBy): self
    {
        $this->addedBy = $addedBy;
        return $this;
    }

    public function getHeaders(): array
    {
        return [
            'option', 'setting'
        ];
    }

    public function getBaseUrl(): string
    {
        return 'thirst.api';
    }

    public function setData(\flexible_table $table): void
    {
        foreach ([
            'Added By'                  => $this->addedBy->email,
            'Expires At'                => (new \DateTime)->setTimestamp($this->accessToken->expires_at)->format('d F Y, h:i:s A'),
            'Organisation Name'         => $this->accessToken->organisation_name,
            'Organisation Subdomain'    => $this->accessToken->organisation_subdomain
        ] as $option => $setting) {
            $table->add_data([$option, $setting]);
        }
    }
}