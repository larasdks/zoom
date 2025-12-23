<?php

namespace laraSDKs\Zoom;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use laraSDKs\Zoom\Exceptions\AuthenticationException;
use laraSDKs\Zoom\Exceptions\NotFoundException;
use laraSDKs\Zoom\Exceptions\ValidationException;
use laraSDKs\Zoom\Exceptions\ZoomApiException;
use laraSDKs\Zoom\Resources\Meetings;
use laraSDKs\Zoom\Resources\Reports;
use laraSDKs\Zoom\Resources\Users;
use laraSDKs\Zoom\Resources\Webinars;

/**
 * Core Zoom API Client.
 */
class Client
{
    protected array $config;

    protected ?string $accessToken = null;

    protected bool $useServerToServerAuth = false;

    protected ?string $serverToServerToken = null;

    protected ?int $serverToServerTokenExpiry = null;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    /**
     * Set the OAuth 2.0 access token for requests.
     *
     * @return $this
     */
    public function setToken(string $token): self
    {
        $this->accessToken = $token;
        $this->useServerToServerAuth = false;

        return $this;
    }

    /**
     * Enable Server-to-Server OAuth authentication.
     *
     * @return $this
     */
    public function setServerToServerAuth(): self
    {
        $this->useServerToServerAuth = true;
        $this->accessToken = null;

        return $this;
    }

    /**
     * Get the Meetings resource service.
     */
    public function meetings(): Meetings
    {
        return new Meetings($this);
    }

    /**
     * Get the Users resource service.
     */
    public function users(): Users
    {
        return new Users($this);
    }

    /**
     * Get the Webinars resource service.
     */
    public function webinars(): Webinars
    {
        return new Webinars($this);
    }

    /**
     * Get the Reports resource service.
     */
    public function reports(): Reports
    {
        return new Reports($this);
    }

    /**
     * Make an authenticated request to the Zoom API.
     *
     * @throws ZoomApiException|ConnectionException
     */
    public function request(string $method, string $endpoint, array $options = []): array
    {
        $url = ltrim($endpoint, '/');

        $headers = [
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        if ($this->useServerToServerAuth) {
            $headers['Authorization'] = 'Bearer '.$this->getServerToServerToken();
        } elseif ($this->accessToken) {
            $headers['Authorization'] = 'Bearer '.$this->accessToken;
        } else {
            throw new AuthenticationException('No authentication method configured. Use setToken() or setServerToServerAuth().');
        }

        $http = Http::withHeaders($headers)
            ->baseUrl($this->config['api']['endpoint'])
            ->timeout($this->config['api']['timeout'] ?? 60);

        if (isset($options['query'])) {
            $http = $http->withQueryParameters($options['query']);
        }

        $response = $http->{$method}($url, $options['data'] ?? []);

        if ($response->failed()) {
            $this->handleError($response);
        }

        $json = $response->json();

        return [
            'data' => $json,
            'pagination' => $this->parsePagination($response->headers()),
        ];
    }

    /**
     * Get or generate a Server-to-Server OAuth token.
     *
     * @throws ConnectionException|AuthenticationException
     */
    protected function getServerToServerToken(): string
    {
        // Check if we have a valid cached token
        if ($this->serverToServerToken && $this->serverToServerTokenExpiry && time() < $this->serverToServerTokenExpiry) {
            return $this->serverToServerToken;
        }

        $s2sConfig = $this->config['server_to_server'] ?? [];

        if (empty($s2sConfig['account_id']) || empty($s2sConfig['client_id']) || empty($s2sConfig['client_secret'])) {
            throw new AuthenticationException('Server-to-Server OAuth credentials not configured.');
        }

        // Exchange credentials for an access token using Server-to-Server OAuth
        $response = Http::withHeaders([
            'Authorization' => 'Basic '.$this->encode(),
        ])->asForm()
            ->post('https://zoom.us/oauth/token', [
                'grant_type' => 'account_credentials',
                'account_id' => $s2sConfig['account_id'],
            ]);

        if ($response->failed()) {
            throw new AuthenticationException('Failed to obtain Server-to-Server OAuth token: '.$response->body());
        }

        $data = $response->json();
        $this->serverToServerToken = $data['access_token'];
        $this->serverToServerTokenExpiry = time() + ($data['expires_in'] ?? 3600) - 60; // 1-minute buffer

        return $this->serverToServerToken;
    }

    private function encode(): string
    {
        return base64_encode(config('services.zoom.client_id').':'.config('services.zoom.client_secret'));
    }

    /**
     * Handle API errors and throw custom exceptions.
     *
     * @throws ZoomApiException
     */
    protected function handleError(Response $response): void
    {
        $status = $response->status();
        $body = $response->json();
        $message = $body['message'] ?? $body['error'] ?? 'API error';
        $errors = $body['errors'] ?? null;

        match ($status) {
            401, 403 => throw new AuthenticationException($message, $status, $errors),
            404 => throw new NotFoundException($message, $status, $errors),
            400, 422 => throw new ValidationException($message, $status, $errors),
            default => throw new ZoomApiException($message, $status, $errors),
        };
    }

    /**
     * Parse pagination from response headers or body.
     */
    protected function parsePagination(array $headers): ?array
    {
        // Zoom API uses cursor-based pagination in response body
        // This method can be extended to parse Link headers if needed
        return null;
    }
}
