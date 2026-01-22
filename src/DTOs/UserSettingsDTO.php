<?php

namespace laraSDKs\Zoom\DTOs;

/**
 * Data Transfer Object for Zoom User Settings.
 */
readonly class UserSettingsDTO
{
    public function __construct(
        public ?array $scheduleMeeting = null,
        public ?array $inMeeting = null,
        public ?array $emailNotification = null,
        public ?array $recording = null,
        public ?array $telephony = null,
        public ?array $feature = null,
        public ?array $tsp = null,
        public ?array $audioConferencing = null,
        public array $rawData = []
    ) {}

    /**
     * Create a UserSettingsDTO from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            scheduleMeeting: $data['schedule_meeting'] ?? null,
            inMeeting: $data['in_meeting'] ?? null,
            emailNotification: $data['email_notification'] ?? null,
            recording: $data['recording'] ?? null,
            telephony: $data['telephony'] ?? null,
            feature: $data['feature'] ?? null,
            tsp: $data['tsp'] ?? null,
            audioConferencing: $data['audio_conferencing'] ?? null,
            rawData: $data
        );
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'schedule_meeting' => $this->scheduleMeeting,
            'in_meeting' => $this->inMeeting,
            'email_notification' => $this->emailNotification,
            'recording' => $this->recording,
            'telephony' => $this->telephony,
            'feature' => $this->feature,
            'tsp' => $this->tsp,
            'audio_conferencing' => $this->audioConferencing,
        ];
    }

    /**
     * Get a specific setting value by path (dot notation).
     * Example: getSetting('schedule_meeting.host_video')
     */
    public function getSetting(string $path, mixed $default = null): mixed
    {
        $keys = explode('.', $path);
        $value = $this->rawData;

        foreach ($keys as $key) {
            if (! isset($value[$key])) {
                return $default;
            }
            $value = $value[$key];
        }

        return $value;
    }

    /**
     * Check if host video is enabled by default in scheduled meetings.
     */
    public function isHostVideoEnabled(): ?bool
    {
        return $this->scheduleMeeting['host_video'] ?? null;
    }

    /**
     * Check if participant video is enabled by default in scheduled meetings.
     */
    public function isParticipantVideoEnabled(): ?bool
    {
        return $this->scheduleMeeting['participants_video'] ?? null;
    }

    /**
     * Check if cloud recording is enabled.
     */
    public function isCloudRecordingEnabled(): ?bool
    {
        return $this->recording['cloud_recording'] ?? null;
    }

    /**
     * Check if local recording is enabled.
     */
    public function isLocalRecordingEnabled(): ?bool
    {
        return $this->recording['local_recording'] ?? null;
    }
}
