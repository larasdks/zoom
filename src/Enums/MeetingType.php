<?php

namespace laraSDKs\Zoom\Enums;

/**
 * Enum for Zoom Meeting Types.
 */
enum MeetingType: int
{
    case INSTANT = 1;
    case SCHEDULED = 2;
    case RECURRING_NO_FIXED_TIME = 3;
    case RECURRING_FIXED_TIME = 8;

    /**
     * Get the human-readable name for the meeting type.
     */
    public function getName(): string
    {
        return match ($this) {
            self::INSTANT => 'Instant Meeting',
            self::SCHEDULED => 'Scheduled Meeting',
            self::RECURRING_NO_FIXED_TIME => 'Recurring Meeting with no fixed time',
            self::RECURRING_FIXED_TIME => 'Recurring Meeting with fixed time',
        };
    }

    /**
     * Check if the meeting type is recurring.
     */
    public function isRecurring(): bool
    {
        return match ($this) {
            self::RECURRING_NO_FIXED_TIME, self::RECURRING_FIXED_TIME => true,
            default => false,
        };
    }

    /**
     * Check if the meeting type is scheduled.
     */
    public function isScheduled(): bool
    {
        return $this === self::SCHEDULED;
    }

    /**
     * Check if the meeting type is instant.
     */
    public function isInstant(): bool
    {
        return $this === self::INSTANT;
    }

    /**
     * Create from integer value, returning null if invalid.
     */
    public static function tryFromInt(?int $value): ?self
    {
        if ($value === null) {
            return null;
        }

        return self::tryFrom($value);
    }
}
