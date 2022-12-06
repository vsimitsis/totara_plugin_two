<?php

namespace ThirstPlugin\Model;

use ThirstPlugin\Traits\UsesDatabase;
use ThirstPlugin\Traits\UsesUser;
use ThirstPlugin\ThirstApi;

class Project extends BaseModel
{
    use UsesDatabase;
    use UsesUser;

    /**
     * Retrieves all projects for currently logged in user based.
     *
     * @param AccessToken $accessToken
     * @return array|null
     * @throws \Exception
     */
    public static function all(AccessToken $accessToken): ?array
    {
        $response = ThirstApi::request('GET', 'api/projects', $accessToken);
        if (!$response->isSuccessful()) {
            return null;
        }

        $projects = [];
        foreach ($response->getBody()->projects as $rawProject) {
            $projects[] = new self($rawProject);
        }
        return $projects;
    }

    /**
     * Returns API urls
     *
     * @return string
     */
    public function apiUrl(): string
    {
        return sprintf('%sapi/projects/%d', ThirstApi::getApiUrl(), $this->id);
    }

    /**
     * Return the project show url
     *
     * @return \moodle_url
     * @throws \moodle_exception
     */
    public function url()
    {
        return new \moodle_url("/local/thirst/project/view.php", ['project_id' => $this->id]);
    }

    /**
     * Searches project in the API based on its id
     *
     * @param integer $projectId
     * @return self|null
     */
    public static function find(int $projectId, AccessToken $accessToken): ?self
    {
        $response = ThirstApi::request('GET', sprintf('api/projects/%d', $projectId), $accessToken);
        if ($response->isSuccessful()) {
            return new self($response->getBody()->project);
        }
        return null;
    }

}