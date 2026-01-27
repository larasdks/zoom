<?php

namespace laraSDKs\Zoom\Resources;

use Illuminate\Http\Client\ConnectionException;
use laraSDKs\Zoom\Client;
use laraSDKs\Zoom\DTOs\ParticipantCollectionDTO;
use laraSDKs\Zoom\Exceptions\ZoomApiException;

/**
 * Service class for Zoom Reports resource.
 */
class Reports
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * List all meeting participants for a meeting.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function meetingParticipants(string $meetingId, array $query = []): ParticipantCollectionDTO
    {
        $response = $this->client->request('get', "/report/meetings/$meetingId/participants", [
            'query' => $query,
        ]);

        return ParticipantCollectionDTO::fromArray($response['data'], $response['pagination']);
    }
}
