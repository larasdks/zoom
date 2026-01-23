<?php

namespace laraSDKs\Zoom\DTOs;

use Carbon\Carbon;
use DateTime;
use laraSDKs\Zoom\Enums\MeetingType;

/**
 * Data Transfer Object for Zoom Meeting.
 */
readonly class MeetingDTO
{
    public function __construct(
        public int $id,
        public string $uuid,
        public string $hostId,
        public ?string $topic = null,
        public ?MeetingType $type = null,
        public ?string $status = null,
        public ?Carbon $startTime = null,
        public ?int $duration = null,
        public ?string $timezone = null,
        public ?string $agenda = null,
        public ?Carbon $createdAt = null,
        public ?string $joinUrl = null,
        public ?string $startUrl = null,
        public ?string $password = null,
        public ?string $h323Password = null,
        public ?string $pstnPassword = null,
        public ?string $encryptedPassword = null,
        public ?string $pmi = null,
        public ?array $settings = null,
        public ?array $recurrence = null,
        public ?array $occurrences = null,
        public ?array $trackingFields = null,
        public ?string $registrationUrl = null,
        public ?bool $preSchedule = null,
        public array $rawData = []
    ) {}

    /**
     * Create a MeetingDTO from API response array.
     *
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            uuid: $data['uuid'] ?? '',
            hostId: $data['host_id'] ?? '',
            topic: $data['topic'] ?? null,
            type: MeetingType::tryFromInt($data['type'] ?? null),
            status: $data['status'] ?? null,
            startTime: isset($data['start_time']) ? Carbon::parse($data['start_time']) : null,
            duration: $data['duration'] ?? null,
            timezone: $data['timezone'] ?? null,
            agenda: $data['agenda'] ?? null,
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            joinUrl: $data['join_url'] ?? null,
            startUrl: $data['start_url'] ?? null,
            password: $data['password'] ?? null,
            h323Password: $data['h323_password'] ?? null,
            pstnPassword: $data['pstn_password'] ?? null,
            encryptedPassword: $data['encrypted_password'] ?? null,
            pmi: $data['pmi'] ?? null,
            settings: $data['settings'] ?? null,
            recurrence: $data['recurrence'] ?? null,
            occurrences: $data['occurrences'] ?? null,
            trackingFields: $data['tracking_fields'] ?? null,
            registrationUrl: $data['registration_url'] ?? null,
            preSchedule: $data['pre_schedule'] ?? null,
            rawData: $data
        );
    }

    /**
     * Get the meeting type name.
     */
    public function getMeetingTypeName(): string
    {
        return $this->type?->getName() ?? 'Unknown';
    }

    /**
     * Check if the meeting is recurring.
     */
    public function isRecurring(): bool
    {
        return $this->type?->isRecurring() ?? false;
    }

    /**
     * Check if the meeting is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this->type?->isScheduled() ?? false;
    }

    /**
     * Check if the meeting is instant.
     */
    public function isInstant(): bool
    {
        return $this->type?->isInstant() ?? false;
    }

    /**
     * Check if the meeting has started.
     */
    public function hasStarted(): bool
    {
        if (! $this->startTime) {
            return false;
        }

        return $this->startTime <= new DateTime;
    }

    /**
     * Check if the meeting has ended.
     */
    public function hasEnded(): bool
    {
        if (! $this->startTime || ! $this->duration) {
            return false;
        }
        $endTime = clone $this->startTime;
        $endTime->modify("+{$this->duration} minutes");

        return $endTime <= new DateTime;
    }

    /**
     * Check if the meeting is currently in progress.
     */
    public function isInProgress(): bool
    {
        return $this->hasStarted() && ! $this->hasEnded();
    }

    /**
     * Get the meeting end time.
     */
    public function getEndTime(): ?DateTime
    {
        if (! $this->startTime || ! $this->duration) {
            return null;
        }
        $endTime = clone $this->startTime;
        $endTime->modify("+{$this->duration} minutes");

        return $endTime;
    }

    /**
     * Get a specific setting value.
     */
    public function getSetting(string $key, mixed $default = null): mixed
    {
        return $this->settings[$key] ?? $default;
    }

    /**
     * Check if waiting room is enabled.
     */
    public function hasWaitingRoom(): bool
    {
        return $this->getSetting('waiting_room', false);
    }

    /**
     * Check if join before host is enabled.
     */
    public function canJoinBeforeHost(): bool
    {
        return $this->getSetting('join_before_host', false);
    }

    /**
     * Check if mute upon entry is enabled.
     */
    public function isMuteUponEntry(): bool
    {
        return $this->getSetting('mute_upon_entry', false);
    }

    /**
     * Check if auto recording is enabled and get the type.
     */
    public function getAutoRecordingType(): ?string
    {
        return $this->getSetting('auto_recording');
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'host_id' => $this->hostId,
            'topic' => $this->topic,
            'type' => $this->type?->value,
            'status' => $this->status,
            'start_time' => $this->startTime?->format('c'),
            'duration' => $this->duration,
            'timezone' => $this->timezone,
            'agenda' => $this->agenda,
            'created_at' => $this->createdAt?->format('c'),
            'join_url' => $this->joinUrl,
            'start_url' => $this->startUrl,
            'password' => $this->password,
            'h323_password' => $this->h323Password,
            'pstn_password' => $this->pstnPassword,
            'encrypted_password' => $this->encryptedPassword,
            'pmi' => $this->pmi,
            'settings' => $this->settings,
            'recurrence' => $this->recurrence,
            'occurrences' => $this->occurrences,
            'tracking_fields' => $this->trackingFields,
            'registration_url' => $this->registrationUrl,
            'pre_schedule' => $this->preSchedule,
        ];
    }
}
