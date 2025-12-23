# Laravel Zoom API SDK

A Laravel-native SDK for the Zoom API. Integrates seamlessly with Laravel 11+, using the HTTP client, service providers, facades, and Socialite for OAuth 2.0 authentication. Supports both OAuth 2.0 and Server-to-Server OAuth authentication methods.

## Installation

```bash
composer require connectors-studio/zoom
```

## Configuration

Publish the config file:

```bash
php artisan vendor:publish --provider="laraSDKs\Zoom\Providers\ZoomServiceProvider" --tag=config
```

Set your credentials in `.env`:

### For OAuth 2.0 Authentication

```
ZOOM_CLIENT_ID=your_client_id
ZOOM_CLIENT_SECRET=your_client_secret
ZOOM_REDIRECT_URI=https://your-app.com/auth/zoom/callback
```

### For Server-to-Server OAuth Authentication

```
ZOOM_ACCOUNT_ID=your_account_id
ZOOM_S2S_CLIENT_ID=your_s2s_client_id
ZOOM_S2S_CLIENT_SECRET=your_s2s_client_secret
```

### Optional Configuration

```
ZOOM_API_ENDPOINT=https://api.zoom.us/v2
ZOOM_TIMEOUT=60
```

## Socialite Setup (OAuth 2.0)

Add to `config/services.php`:

```php
'zoom' => [
    'client_id' => env('ZOOM_CLIENT_ID'),
    'client_secret' => env('ZOOM_CLIENT_SECRET'),
    'redirect' => env('ZOOM_REDIRECT_URI'),
],
```

Register the custom Socialite provider in your `AppServiceProvider`:

```php
use laraSDKs\Zoom\Socialite\ZoomProvider;
use Laravel\Socialite\Contracts\Factory;

public function boot(): void
{
    $socialite = $this->app->make(Factory::class);
    $socialite->extend('zoom', function ($app) use ($socialite) {
        $config = $app['config']['services.zoom'];
        return $socialite->buildProvider(ZoomProvider::class, $config);
    });
}
```

## Authentication Guide

### OAuth 2.0 Authentication

Example controller for Socialite login:

```php
use Laravel\Socialite\Facades\Socialite;
use laraSDKs\Zoom\Facades\Zoom;

public function redirectToZoom()
{
    return Socialite::driver('zoom')->redirect();
}

public function handleZoomCallback()
{
    $user = Socialite::driver('zoom')->user();
    
    // Store $user->token in your database
    $accessToken = $user->token;
    
    // Use the token for API calls
    Zoom::setToken($accessToken);
    
    // Now you can make API calls
    $meetings = Zoom::meetings()->list('me');
}
```

### Server-to-Server OAuth Authentication

Server-to-Server OAuth doesn't require user interaction and is ideal for server-side operations:

```php
use laraSDKs\Zoom\Facades\Zoom;

// Enable Server-to-Server authentication
Zoom::setServerToServerAuth();

// Now you can make API calls without user tokens
$users = Zoom::users()->list();
```

## Usage Examples

### Meetings

```php
use laraSDKs\Zoom\Facades\Zoom;

// Set authentication (OAuth 2.0)
Zoom::setToken($userAccessToken);

// Or use Server-to-Server OAuth
Zoom::setServerToServerAuth();

// Create a meeting
$meeting = Zoom::meetings()->create('me', [
    'topic' => 'Team Standup',
    'type' => 2, // Scheduled meeting
    'start_time' => '2024-01-15T10:00:00Z',
    'duration' => 30,
    'timezone' => 'America/New_York',
    'settings' => [
        'host_video' => true,
        'participant_video' => true,
    ],
]);

// List all meetings for a user
$meetings = Zoom::meetings()->list('me', [
    'type' => 'scheduled',
    'page_size' => 30,
]);

// Get a meeting by ID
$meeting = Zoom::meetings()->get($meetingId);

// Update a meeting
Zoom::meetings()->update($meetingId, [
    'topic' => 'Updated Meeting Topic',
]);

// Delete a meeting
Zoom::meetings()->delete($meetingId);

// Add a registrant to a meeting
$registrant = Zoom::meetings()->addRegistrant($meetingId, [
    'email' => 'participant@example.com',
    'first_name' => 'John',
    'last_name' => 'Doe',
]);

// List meeting registrants
$registrants = Zoom::meetings()->listRegistrants($meetingId);
```

### Users

```php
use laraSDKs\Zoom\Facades\Zoom;

// List all users
$users = Zoom::users()->list([
    'status' => 'active',
    'page_size' => 30,
]);

// Get a user by ID or email
$user = Zoom::users()->get('user@example.com');

// Create a new user
$user = Zoom::users()->create([
    'action' => 'create',
    'user_info' => [
        'email' => 'newuser@example.com',
        'type' => 1, // Basic user
        'first_name' => 'Jane',
        'last_name' => 'Doe',
    ],
]);

// Update a user
Zoom::users()->update($userId, [
    'first_name' => 'Updated Name',
]);

// Delete a user
Zoom::users()->delete($userId, [
    'action' => 'delete',
]);

// Get user settings
$settings = Zoom::users()->getSettings($userId);

// Update user settings
Zoom::users()->updateSettings($userId, [
    'schedule_meeting' => [
        'host_video' => true,
    ],
]);
```

### Webinars

```php
use laraSDKs\Zoom\Facades\Zoom;

// Create a webinar
$webinar = Zoom::webinars()->create('me', [
    'topic' => 'Product Launch Webinar',
    'type' => 5, // Webinar
    'start_time' => '2024-01-20T14:00:00Z',
    'duration' => 60,
    'timezone' => 'America/New_York',
]);

// List all webinars for a user
$webinars = Zoom::webinars()->list('me');

// Get a webinar by ID
$webinar = Zoom::webinars()->get($webinarId);

// Update a webinar
Zoom::webinars()->update($webinarId, [
    'topic' => 'Updated Webinar Topic',
]);

// Delete a webinar
Zoom::webinars()->delete($webinarId);

// Add a registrant to a webinar
$registrant = Zoom::webinars()->addRegistrant($webinarId, [
    'email' => 'attendee@example.com',
    'first_name' => 'Alice',
    'last_name' => 'Smith',
]);

// List webinar registrants
$registrants = Zoom::webinars()->listRegistrants($webinarId);
```

### Reports

```php
use laraSDKs\Zoom\Facades\Zoom;

// Get meeting participants report
$participants = Zoom::reports()->getMeetingParticipants($meetingId, [
    'page_size' => 30,
]);

// Get a meeting detail report
$meetingDetail = Zoom::reports()->getMeetingDetail($meetingId);

// Get daily usage report
$dailyUsage = Zoom::reports()->getDailyUsage([
    'year' => 2024,
    'month' => 1,
]);

// Get webinar participants report
$webinarParticipants = Zoom::reports()->getWebinarParticipants($webinarId);

// Get webinar detail report
$webinarDetail = Zoom::reports()->getWebinarDetail($webinarId);

// Get user activity report
$userActivity = Zoom::reports()->getUserActivity([
    'from' => '2024-01-01',
    'to' => '2024-01-31',
]);

// Get user activity for a specific user
$userActivity = Zoom::reports()->getUserActivityByUser($userId, [
    'from' => '2024-01-01',
    'to' => '2024-01-31',
]);
```

## Error Handling

The package provides specific exception types for different error scenarios:

```php
use laraSDKs\Zoom\Exceptions\ZoomApiException;
use laraSDKs\Zoom\Exceptions\AuthenticationException;
use laraSDKs\Zoom\Exceptions\NotFoundException;
use laraSDKs\Zoom\Exceptions\ValidationException;

try {
    $meeting = Zoom::meetings()->get($meetingId);
} catch (AuthenticationException $e) {
    // Handle authentication errors (401, 403)
    logger()->error('Zoom authentication failed: ' . $e->getMessage());
} catch (NotFoundException $e) {
    // Handle not found errors (404)
    logger()->warning('Meeting not found: ' . $e->getMessage());
} catch (ValidationException $e) {
    // Handle validation errors (400, 422)
    $errors = $e->getErrors();
    logger()->error('Validation failed', ['errors' => $errors]);
} catch (ZoomApiException $e) {
    // Handle other API errors
    logger()->error('Zoom API error: ' . $e->getMessage());
}
```

## Authentication Methods

### OAuth 2.0
- User-based authentication
- Requires user consent
- Access tokens are user-specific
- Use when you need to act on behalf of users

### Server-to-Server OAuth
- Application-level authentication
- No user interaction required
- Uses account-level credentials
- Use for server-side operations and administrative tasks

## Pagination

Zoom API uses cursor-based pagination. The response includes pagination information in the response body. Check the `next_page_token` field in the response to fetch the next page:

```php
$response = Zoom::meetings()->list('me', ['page_size' => 30]);
// Handle pagination manually based on response structure
```

## Requirements

- PHP >= 8.1
- Laravel >= 11.0 or >= 10.0
- Zoom API credentials (OAuth 2.0 or Server-to-Server OAuth)

## License

MIT

---

**See the source code for full PHPDoc documentation.**

