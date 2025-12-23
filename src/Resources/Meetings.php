<?php

namespace laraSDKs\Zoom\Resources;

use Illuminate\Http\Client\ConnectionException;
use laraSDKs\Zoom\Client;
use laraSDKs\Zoom\Exceptions\ZoomApiException;

/**
 * Service class for Zoom Meetings resource.
 */
class Meetings
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Create a meeting for a user.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function create(string $userId, array $data): array
    {
        return $this->client->request('post', "users/$userId/meetings", [
            'data' => $data,
        ])['data'];
    }

    /**
     * List all meetings for a user.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function list(string $userId, array $query = []): array
    {
        return $this->client->request('get', "users/$userId/meetings", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Get a meeting by ID.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function get(int $meetingId, array $query = []): array
    {
        return $this->client->request('get', "meetings/$meetingId", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Update a meeting.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function update(int $meetingId, array $data): array
    {
        return $this->client->request('patch', "meetings/$meetingId", [
            'data' => $data,
        ])['data'];
    }

    /**
     * Delete a meeting.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function delete(int $meetingId, array $query = []): void
    {
        $this->client->request('delete', "meetings/$meetingId", [
            'query' => $query,
        ]);
    }

    /**
     * Add a registrant to a meeting.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function addRegistrant(int $meetingId, array $data): array
    {
        return $this->client->request('post', "meetings/$meetingId/registrants", [
            'data' => $data,
        ])['data'];
    }

    /**
     * List meeting registrants.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function listRegistrants(int $meetingId, array $query = []): array
    {
        return $this->client->request('get', "meetings/$meetingId/registrants", [
            'query' => $query,
        ])['data'];
    }
}
