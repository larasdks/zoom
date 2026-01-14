<?php

namespace laraSDKs\Zoom\Resources;

use Illuminate\Http\Client\ConnectionException;
use laraSDKs\Zoom\Client;
use laraSDKs\Zoom\Exceptions\ZoomApiException;

/**
 * Service class for Zoom Meetings resource.
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
    public function meetingParticipants(string $meetingId, array $query = []): array
    {
        return $this->client->request('get', "/report/meetings/$meetingId/participants", [
            'query' => $query,
        ])['data'];
    }
}
