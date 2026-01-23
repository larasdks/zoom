<?php

namespace laraSDKs\Zoom\Resources;

use Illuminate\Http\Client\ConnectionException;
use laraSDKs\Zoom\Client;
use laraSDKs\Zoom\DTOs\MeetingCollectionDTO;
use laraSDKs\Zoom\DTOs\MeetingDTO;
use laraSDKs\Zoom\DTOs\MeetingRegistrantCollectionDTO;
use laraSDKs\Zoom\DTOs\MeetingRegistrantDTO;
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
    public function create(string $userId, array $data): MeetingDTO
    {
        $response = $this->client->request('post', "users/$userId/meetings", [
            'data' => $data,
        ]);

        return MeetingDTO::fromArray($response['data']);
    }

    /**
     * List all meetings for a user.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function list(string $userId, array $query = []): MeetingCollectionDTO
    {
        $response = $this->client->request('get', "users/$userId/meetings", [
            'query' => $query,
        ]);

        return MeetingCollectionDTO::fromArray($response['data'], $response['pagination']);
    }

    /**
     * Get a meeting by ID.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function get(int $meetingId, array $query = []): MeetingDTO
    {
        $response = $this->client->request('get', "meetings/$meetingId", [
            'query' => $query,
        ]);

        return MeetingDTO::fromArray($response['data']);
    }

    /**
     * Update a meeting.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function update(int $meetingId, array $data): void
    {
        $this->client->request('patch', "meetings/$meetingId", [
            'data' => $data,
        ]);
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
    public function addRegistrant(int $meetingId, array $data): MeetingRegistrantDTO
    {
        $response = $this->client->request('post', "meetings/$meetingId/registrants", [
            'data' => $data,
        ]);

        return MeetingRegistrantDTO::fromArray($response['data']);
    }

    /**
     * List meeting registrants.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function listRegistrants(int $meetingId, array $query = []): MeetingRegistrantCollectionDTO
    {
        $response = $this->client->request('get', "meetings/$meetingId/registrants", [
            'query' => $query,
        ]);

        return MeetingRegistrantCollectionDTO::fromArray($response['data'], $response['pagination']);
    }
}
