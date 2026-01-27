<?php

namespace laraSDKs\Zoom\Enums;

/**
 * Enum for Zoom Participant Status.
 */
enum ParticipantStatus: string
{
    case IN_MEETING = 'in_meeting';
    case IN_WAITING_ROOM = 'in_waiting_room';

    /**
     * Get the human-readable name for the participant status.
     */
    public function getName(): string
    {
        return match ($this) {
            self::IN_MEETING => 'In Meeting',
            self::IN_WAITING_ROOM => 'In Waiting Room',
        };
    }

    /**
     * Check if the participant is in the meeting.
     */
    public function isInMeeting(): bool
    {
        return $this === self::IN_MEETING;
    }

    /**
     * Check if the participant is in the waiting room.
     */
    public function isInWaitingRoom(): bool
    {
        return $this === self::IN_WAITING_ROOM;
    }

    /**
     * Create from string value, returning null if invalid.
     */
    public static function tryFromString(?string $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}
