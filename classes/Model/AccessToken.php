<?php

namespace ThirstPlugin\Model;

use ThirstPlugin\Traits\IsResource;
use ThirstPlugin\Traits\UsesDatabase;
use ThirstPlugin\Traits\UsesUser;
use ThirstPlugin\ThirstApi;

class AccessToken extends BaseModel
{
    use UsesDatabase;
    use UsesUser;
    use IsResource;

    /**
     * Get the table name of where the resource is stored
     *
     * @return string
     */
    protected static function getTable(): string
    {
        return 'thirst_access_tokens';
    }

    /**
     * Returns access token for a given user id
     *
     * @param integer $addedBy
     * @return self|null
     */
    public static function getByAddedBy(int $addedBy): ?self
    {
        $result = self::db()->get_record(self::getTable(), [
            'added_by' => $addedBy
        ]);

        return $result ? (new self)->populate($result) : null;
    }

    /**
     * Request new token from thirst API
     *
     * @param \stdClass $credentials
     * @return null|AccessToken
     * @throws \Exception
     */
    public static function request(\stdClass $credentials): ?self
    {
        // Before requesting token, make sure that org is not set already
        $existingToken = self::get(['organisation_subdomain' => ThirstApi::getOrganisation()]);
        if ($existingToken) {
            throw new \Exception(sprintf('Token already configured for subdomain %s.', ThirstApi::getOrganisation()));
        }

        // Request new token via API using passed credentials
        $authResponse = ThirstApi::request('POST', 'api/auth-lms', null, [
            'form_params' => [
                'grant_type' => 'password',
                'username'   => $credentials->username,
                'password'   => $credentials->password
            ]
        ]);

        if ($authResponse->isSuccessful()) {
            // Get body
            $authResponseBody = $authResponse->getBody();
            // Fetch organisation details
            $orgResponse = ThirstApi::request('GET', 'api/organisation', null, [
                'headers' => [
                    'Authorization' => sprintf('%s %s', $authResponseBody->token_type, $authResponseBody->access_token)
                ]
            ]);

            // Make sure that response is successful
            if (!$orgResponse->isSuccessful()) {
                return null;
            }

            // Get the response body
            $orgResponseBody = $orgResponse->getBody();

            // Instantiate access token and update token in the database
            // Response will contain token_type, expires_in, access_token, refresh_token
            $accessToken = new self([
                'added_by'                  => self::user()->id,
                'token_type'                => $authResponseBody->token_type,
                'access_token'              => $authResponseBody->access_token,
                'refresh_token'             => $authResponseBody->refresh_token,
                'expires_at'                => self::computeExpiresAt($authResponseBody->expires_in),
                'organisation_name'         => $orgResponseBody->organisation->name,
                'organisation_subdomain'    => $orgResponseBody->organisation->subdomain,
                'created_at'                => time()
            ]);

            // We only want to one token for the user id to be present
            $accessToken->create();
            return $accessToken;
        }

        return null;
    }

    /**
     * Refreshes the token
     *
     * @return AccessToken|null
     */
    public function refresh(): ?self
    {
        // First, check if the refresh can be performed
        if (!$this->refresh_token || !$this->organisation_subdomain) {
            return null;
        }

        // Once confirmed, request API to get the refresh token
        ThirstApi::setOrganisation($this->organisation_subdomain);
        $authResponse = ThirstApi::request('POST', 'api/auth', null, [
            'form_params' => [
                'grant_type'    => 'refresh_token',
                'refresh_token' => $this->refresh_token
            ]
        ]);

        // Check if the request has been successful
        if (!$authResponse->isSuccessful()) {
            return null;
        }

        // Once confirmed, update the token details
        $authResponseBody = $authResponse->getBody();
        // Repopulate new details
        $this->populate([
            'token_type'    => $authResponseBody->token_type,
            'access_token'  => $authResponseBody->access_token,
            'refresh_token' => $authResponseBody->refresh_token,
            'expires_at'    => self::computeExpiresAt($authResponseBody->expires_in)
        ]);

        // Once populated, update it
        $this->update();

        return $this;
    }

    /**
     * Creates a token from an API response
     *
     * @param \stdClass $response
     * @return self
     */
    private function createFromResponse(\stdClass $response): self
    {
        return new self([
            'token_type'    => $response->token_type,
            'access_token'  => $response->access_token,
            'refresh_token' => $response->refresh_token,
            'expires_at'    => self::computeExpiresAt($response->expires_in),
            'created_at'    => time()
        ]);
    }

    /**
     * Check if the token needs refreshing by checking if expires_at is within a day
     *
     * @return boolean
     */
    public function needsRefreshing(): bool
    {
        $now = new \DateTime('now');
        $expiresAt = (new \Datetime())->setTimestamp($this->expires_at);
        $interval = $now->diff($expiresAt);
        return intval($interval->format('%r%a')) < 1;
    }

    /**
     * Retrieves the token for currently logged in user
     *
     * @return self|null
     */
    public static function getForCurrentUser(): ?self
    {
        return self::getByAddedBy(self::user()->id);
    }

    /**
     * Calculates the timestamp
     *
     * @param integer $expiresIn
     * @return integer
     */
    private static function computeExpiresAt(int $expiresIn): int
    {
        return time() + $expiresIn;
    }

    /**
     * Confirms if the token is valid
     *
     * @return boolean
     */
    public static function confirm(): bool
    {
        $accessToken = self::getForCurrentUser();
        return $accessToken && $accessToken->expires_at > time();
    }

    /**
     * Get the totara/moodle URL
     *
     * @return string
     */
    public function url(): string
    {
        return '/local/thirst/accesstoken/view.php?' . http_build_query(['id' => $this->id]);
    }

    /**
     * Get the totara/moodle delete URL
     *
     * @return string
     */
    public function deleteUrl(): string
    {
        return '/local/thirst/accesstoken/delete.php?' . http_build_query(['id' => $this->id]);
    }

    /**
     * Returns an array of projects that link to the token
     *
     * @return null|array
     */
    public function getProjects(): ?array
    {
        return Project::all($this);
    }

    /**
     * Gets the user by added_by parameter
     *
     * @return User|null
     */
    public function getAddedByUser(): ?User
    {
        return User::find($this->added_by);
    }

    /**
     * Returns list of activities that are assigned this token
     *
     * @return array|null
     */
    public function getActivities(): ?array
    {
        return Activity::getByAccessTokenId($this->id);
    }
}