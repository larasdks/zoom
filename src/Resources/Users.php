<?php

namespace laraSDKs\Zoom\Resources;

use Illuminate\Http\Client\ConnectionException;
use laraSDKs\Zoom\Client;
use laraSDKs\Zoom\DTOs\UserCollectionDTO;
use laraSDKs\Zoom\DTOs\UserDTO;
use laraSDKs\Zoom\DTOs\UserSettingsDTO;
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
    public function list(array $query = []): UserCollectionDTO
    {
        $response = $this->client->request('get', 'users', [
            'query' => $query,
        ]);

        return UserCollectionDTO::fromArray($response['data'], $response['pagination']);
    }

    /**
     * Get a user by ID or email.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function get(string $userId, array $query = []): UserDTO
    {
        $response = $this->client->request('get', "users/$userId", [
            'query' => $query,
        ]);

        return UserDTO::fromArray($response['data']);
    }

    /**
     * Create a new user.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function create(array $data): UserDTO
    {
        $response = $this->client->request('post', 'users', [
            'data' => $data,
        ]);

        return UserDTO::fromArray($response['data']);
    }

    /**
     * Update a user.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function update(string $userId, array $data): void
    {
        $this->client->request('patch', "users/$userId", [
            'data' => $data,
        ]);
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
    public function getSettings(string $userId, array $query = []): UserSettingsDTO
    {
        $response = $this->client->request('get', "users/$userId/settings", [
            'query' => $query,
        ]);

        return UserSettingsDTO::fromArray($response['data']);
    }

    /**
     * Update user settings.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function updateSettings(string $userId, array $data): void
    {
        $this->client->request('patch', "users/$userId/settings", [
            'data' => $data,
        ]);
    }
}
