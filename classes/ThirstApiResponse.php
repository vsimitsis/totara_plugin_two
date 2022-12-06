<?php

namespace ThirstPlugin;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Exception\ClientException;

class ThirstApiResponse
{
    /** @var Response $rawResponse */
    private $rawResponse;

    /**
     * Constructor for the response
     *
     * @param Response $response
     */
    public function __construct(Response $response)
    {
        $this->rawResponse = $response;
    }

    /**
     * Returns raw response if set
     *
     * @return Response|null
     */
    public function getRaw(): ?Response
    {
        return $this->rawResponse;
    }

    /**
     * Check if the response is successful
     *
     * @return boolean
     */
    public function isSuccessful(): bool
    {
        return $this->getRaw()->getStatusCode() === 200;
    }

    /**
     * Get the body of the response
     *
     * @param boolean $asArray
     * @return \stdClass|null
     */
    public function getBody(bool $asArray = false): ?\stdClass
    {
        return json_decode($this->getRaw()->getBody(), $asArray) ?? null;
    }

    /**
     * Creates ThirstApiResponse from ClientException
     *
     * @param ClientException $exception
     * @return self
     */
    public static function fromClientException(ClientException $exception): self
    {
        return new self($exception->getResponse());
    }
}