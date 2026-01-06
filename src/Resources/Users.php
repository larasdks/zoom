<?php

namespace laraSDKs\Zoom\Resources;

use Illuminate\Http\Client\ConnectionException;
use laraSDKs\Zoom\Client;
use laraSDKs\Zoom\Exceptions\ZoomApiException;

/**
 * Service class for Zoom Users resource.
 */
class Users
{
    protected Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * List all users.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function list(array $query = []): array
    {
        echo "\nEEEEE\n";

        return $this->client->request('get', 'users', [
            'query' => $query,
        ])['data'];
    }

    /**
     * Get a user by ID or email.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function get(string $userId, array $query = []): array
    {
        return $this->client->request('get', "users/$userId", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Create a new user.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function create(array $data): array
    {
        return $this->client->request('post', 'users', [
            'data' => $data,
        ])['data'];
    }

    /**
     * Update a user.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function update(string $userId, array $data): array
    {
        return $this->client->request('patch', "users/$userId", [
            'data' => $data,
        ])['data'];
    }

    /**
     * Delete a user.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function delete(string $userId, array $query = []): void
    {
        $this->client->request('delete', "users/$userId", [
            'query' => $query,
        ]);
    }

    /**
     * Get user settings.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function getSettings(string $userId, array $query = []): array
    {
        return $this->client->request('get', "users/$userId/settings", [
            'query' => $query,
        ])['data'];
    }

    /**
     * Update user settings.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function updateSettings(string $userId, array $data): array
    {
        return $this->client->request('patch', "users/$userId/settings", [
            'data' => $data,
        ])['data'];
    }
}
