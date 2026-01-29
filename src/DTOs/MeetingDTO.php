<?php

namespace laraSDKs\Zoom\DTOs;

use Carbon\Carbon;
use DateTime;
use laraSDKs\Zoom\Enums\MeetingType;

/**
 * Data Transfer Object for Zoom Meeting.
 *
 * All fields from official Zoom Meetings API documentation:
 * - id: Meeting ID (int64)
 * - uuid: Unique Meeting ID
 * - host_id: ID of the user who is set as host
 * - topic: Meeting topic
 * - type: Meeting type (1=Instant, 2=Scheduled, 3=Recurring no fixed time, 8=Recurring fixed time)
 * - agenda: Meeting description (truncated to 250 chars in list)
 * - created_at: Time of creation
 * - duration: Meeting duration in minutes
 * - start_time: Meeting start time
 * - timezone: Timezone to format stardsft time
 * - join_url: URL to join the meeting
 * - pmi: Personal meeting ID (only if PMI was used to schedule)
 */
readonly class MeetingDTO
{
    public function __construct(
        public int $id,
        public string $uuid,
        public string $hostId,
        public ?string $topic = null,
        public ?MeetingType $type = null,
        public ?string $agenda = null,
        public ?Carbon $createdAt = null,
        public ?int $duration = null,
        public ?Carbon $startTime = null,
        public ?string $timezone = null,
        public ?string $joinUrl = null,
        public ?string $pmi = null,
        public array $rawData = []
    ) {}

    /**
     * Create a MeetingDTO from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? 0,
            uuid: $data['uuid'] ?? '',
            hostId: $data['host_id'] ?? '',
            topic: $data['topic'] ?? null,
            type: MeetingType::tryFromInt($data['type'] ?? null),
            agenda: $data['agenda'] ?? null,
            createdAt: isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            duration: $data['duration'] ?? null,
            startTime: isset($data['start_time']) ? Carbon::parse($data['start_time']) : null,
            timezone: $data['timezone'] ?? null,
            joinUrl: $data['join_url'] ?? null,
            pmi: $data['pmi'] ?? null,
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
        $endTime->modify("+$this->duration minutes");

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
        $endTime->modify("+$this->duration minutes");

        return $endTime;
    }

    /**
     * Check if this is a Personal Meeting (PMI).
     */
    public function isPMI(): bool
    {
        return ! empty($this->pmi);
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
            'agenda' => $this->agenda,
            'created_at' => $this->createdAt?->format('c'),
            'duration' => $this->duration,
            'start_time' => $this->startTime?->format('c'),
            'timezone' => $this->timezone,
            'join_url' => $this->joinUrl,
            'pmi' => $this->pmi,
        ];
    }
}
