<?php

namespace laraSDKs\Zoom\Resources;

use laraSDKs\Zoom\Client;
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
     * Get meeting participants report.
     *
     * @throws ZoomApiException
     */
    public function getMeetingParticipants(int $meetingId, array $query = []): array
    {
        return $this->client->request('get', "report/meetings/{$meetingId}/participants", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Get meeting detail report.
     *
     * @throws ZoomApiException
     */
    public function getMeetingDetail(int $meetingId, array $query = []): array
    {
        return $this->client->request('get', "report/meetings/{$meetingId}", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Get daily usage report.
     *
     * @throws ZoomApiException
     */
    public function getDailyUsage(array $query = []): array
    {
        return $this->client->request('get', 'report/daily', [
            'query' => $query,
        ])['data'];
    }

    /**
     * Get webinar participants report.
     *
     * @throws ZoomApiException
     */
    public function getWebinarParticipants(int $webinarId, array $query = []): array
    {
        return $this->client->request('get', "report/webinars/{$webinarId}/participants", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Get webinar detail report.
     *
     * @throws ZoomApiException
     */
    public function getWebinarDetail(int $webinarId, array $query = []): array
    {
        return $this->client->request('get', "report/webinars/{$webinarId}", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Get user activity report.
     *
     * @throws ZoomApiException
     */
    public function getUserActivity(array $query = []): array
    {
        return $this->client->request('get', 'report/users', [
            'query' => $query,
        ])['data'];
    }

    /**
     * Get user activity report for a specific user.
     *
     * @throws ZoomApiException
     */
    public function getUserActivityByUser(string $userId, array $query = []): array
    {
        return $this->client->request('get', "report/users/{$userId}", [
            'query' => $query,
        ])['data'];
    }
}
