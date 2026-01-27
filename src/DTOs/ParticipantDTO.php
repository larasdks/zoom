<?php

namespace laraSDKs\Zoom\DTOs;

use Carbon\Carbon;
use laraSDKs\Zoom\Enums\ParticipantStatus;

/**
 * Data Transfer Object for Zoom Meeting Participant.
 *
 * All fields from official Zoom Reports API documentation:
 * - id: Participant's UUID (deprecated, use participant_user_id)
 * - user_id: Unique ID for this meeting session only
 * - participant_user_id: Persistent UUID across meetings
 * - name: Display name
 * - user_email: Email address
 * - join_time: When participant joined
 * - leave_time: When participant left
 * - duration: Time in meeting (seconds)
 * - status: Current status (in_meeting, in_waiting_room)
 * - customer_key: SDK identifier (max 35 chars)
 * - registrant_id: If meeting requires registration
 * - bo_mtg_id: Breakout room ID if applicable
 * - failover: If failover occurred
 */
readonly class ParticipantDTO
{
    public function __construct(
        public string $id,
        public ?string $userId = null,
        public ?string $participantUserId = null,
        public ?string $name = null,
        public ?string $userEmail = null,
        public ?Carbon $joinTime = null,
        public ?Carbon $leaveTime = null,
        public ?int $duration = null,
        public ?ParticipantStatus $status = null,
        public ?string $customerKey = null,
        public ?string $registrantId = null,
        public ?string $boMtgId = null,
        public ?bool $failover = null,
        public array $rawData = []
    ) {}

    /**
     * Create a ParticipantDTO from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            userId: $data['user_id'] ?? null,
            participantUserId: $data['participant_user_id'] ?? null,
            name: $data['name'] ?? null,
            userEmail: $data['user_email'] ?? null,
            joinTime: isset($data['join_time']) ? Carbon::parse($data['join_time']) : null,
            leaveTime: isset($data['leave_time']) ? Carbon::parse($data['leave_time']) : null,
            duration: $data['duration'] ?? null,
            status: ParticipantStatus::tryFromString($data['status'] ?? null),
            customerKey: $data['customer_key'] ?? null,
            registrantId: $data['registrant_id'] ?? null,
            boMtgId: $data['bo_mtg_id'] ?? null,
            failover: $data['failover'] ?? null,
            rawData: $data
        );
    }

    /**
     * Get the total session duration in minutes.
     */
    public function getDurationInMinutes(): ?int
    {
        if ($this->duration === null) {
            return null;
        }

        return (int) ceil($this->duration / 60);
    }

    /**
     * Get the total session duration in hours.
     */
    public function getDurationInHours(): ?float
    {
        if ($this->duration === null) {
            return null;
        }

        return round($this->duration / 3600, 2);
    }

    /**
     * Check if the participant is still in the meeting (hasn't left yet).
     * This checks if leave_time is null.
     */
    public function isInMeeting(): bool
    {
        return $this->leaveTime === null;
    }

    /**
     * Check if the participant has left the meeting.
     * This checks if leave_time is set.
     */
    public function hasLeft(): bool
    {
        return $this->leaveTime !== null;
    }

    /**
     * Check if the participant's status is "in_meeting".
     */
    public function hasInMeetingStatus(): bool
    {
        return $this->status?->isInMeeting() ?? false;
    }

    /**
     * Check if the participant's status is "in_waiting_room".
     */
    public function isInWaitingRoom(): bool
    {
        return $this->status?->isInWaitingRoom() ?? false;
    }

    /**
     * Get the status name.
     */
    public function getStatusName(): string
    {
        return $this->status?->getName() ?? 'Unknown';
    }

    /**
     * Check if the participant is/was in a breakout room.
     */
    public function isInBreakoutRoom(): bool
    {
        return ! empty($this->boMtgId);
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->userId,
            'participant_user_id' => $this->participantUserId,
            'name' => $this->name,
            'user_email' => $this->userEmail,
            'join_time' => $this->joinTime?->format('c'),
            'leave_time' => $this->leaveTime?->format('c'),
            'duration' => $this->duration,
            'status' => $this->status?->value,
            'customer_key' => $this->customerKey,
            'registrant_id' => $this->registrantId,
            'bo_mtg_id' => $this->boMtgId,
            'failover' => $this->failover,
        ];
    }
}
