<?php

namespace laraSDKs\Zoom\Resources;

use laraSDKs\Zoom\Client;
use laraSDKs\Zoom\Exceptions\ZoomApiException;

/**
 * Service class for Zoom Webinars resource.
 */
class Webinars
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a webinar for a user.
     *
     * @throws ZoomApiException
     */
    public function create(string $userId, array $data): array
    {
        return $this->client->request('post', "users/{$userId}/webinars", [
            'data' => $data,
        ])['data'];
    }

    /**
     * List all webinars for a user.
     *
     * @throws ZoomApiException
     */
    public function list(string $userId, array $query = []): array
    {
        return $this->client->request('get', "users/{$userId}/webinars", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Get a webinar by ID.
     *
     * @throws ZoomApiException
     */
    public function get(int $webinarId, array $query = []): array
    {
        return $this->client->request('get', "webinars/{$webinarId}", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Update a webinar.
     *
     * @throws ZoomApiException
     */
    public function update(int $webinarId, array $data): array
    {
        return $this->client->request('patch', "webinars/{$webinarId}", [
            'data' => $data,
        ])['data'];
    }

    /**
     * Delete a webinar.
     *
     * @throws ZoomApiException
     */
    public function delete(int $webinarId, array $query = []): void
    {
        $this->client->request('delete', "webinars/{$webinarId}", [
            'query' => $query,
        ]);
    }

    /**
     * Add a registrant to a webinar.
     *
     * @throws ZoomApiException
     */
    public function addRegistrant(int $webinarId, array $data): array
    {
        return $this->client->request('post', "webinars/{$webinarId}/registrants", [
            'data' => $data,
        ])['data'];
    }

    /**
     * List webinar registrants.
     *
     * @throws ZoomApiException
     */
    public function listRegistrants(int $webinarId, array $query = []): array
    {
        return $this->client->request('get', "webinars/{$webinarId}/registrants", [
            'query' => $query,
        ])['data'];
    }
}
