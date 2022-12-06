<?php

namespace ThirstPlugin;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use ThirstPlugin\Model\AccessToken;

class ThirstApi
{
    /** @var Client $client */
    private static $client;

    /** @var string $organisation */
    private static $organisation;

    /**
     * Return the API url based on the organisation
     *
     * @return string
     * @todo Make is work with access token
     */
    public static function getApiUrl()
    {
        return sprintf(Config::get('api.endpoint'), self::getOrganisation());
    }

    /**
     * Set the organisation for ThirstApi to use as a subdomain
     *
     * @param string $organisation
     * @return void
     * @throws InvalidArgumentException if the organisation is in invalid format
     */
    public static function setOrganisation(string $organisation): void
    {
        if (!preg_match('/^[a-z][a-z0-9_-]*$/', $organisation)) {
            throw new \InvalidArgumentException(get_string('invalid_organisation', 'local_thirst'));
        }
        self::$organisation = $organisation;
    }

    /**
     * Gets the organisation if set
     *
     * @return string|null
     */
    public static function getOrganisation(): ?string
    {
        return self::$organisation;
    }

    /**
     * Sends a request to the thirst API with authentication token
     *
     * @param string $method
     * @param string $path
     * @param array $details
     * @return ThirstApiResponse
     * @throws \Exception
     */
    public static function request(string $method, string $path, AccessToken $accessToken = null, array $details = []): ThirstApiResponse
    {
        if ($accessToken) {
            $details = array_merge_recursive(
                $details,
                [ 'headers' => [
                    'Authorization' => sprintf('%s %s', $accessToken->token_type, $accessToken->access_token)
                ]]
            );

            // Make sure correct org is set
            ThirstApi::setOrganisation($accessToken->organisation_subdomain);
        }

        // Convert HTTP method to the class method
        $method = strtolower($method);

        // Make a call
        try {
            return new ThirstApiResponse(
                self::client()->$method(self::getApiUrl() . $path,
                self::mergeDefaultOptions($details))
            );
        } catch (ClientException $e) {
            return ThirstApiResponse::fromClientException($e);
        }
    }

    /**
     * Gets the Client instance, and if not set, instantiates new one
     *
     * @return Client
     */
    private static function client(): Client
    {
        if (!self::$client) {
            self::$client = new Client;
        }
        return self::$client;
    }

    /**
     * Returns default headers
     *
     * @return array
     */
    private static function getDefaultHeaders(): array
    {
        return [
            'headers' => [
                'Accept' => 'application/json',
            ]
        ];
    }

    /**
     * Merges default options for the http request with custom ones
     *
     * @param array $options
     * @return array
     */
    private static function mergeDefaultOptions(array $options): array
    {
        return array_merge_recursive(
            self::getDefaultHeaders(),
            $options
        );
    }
}