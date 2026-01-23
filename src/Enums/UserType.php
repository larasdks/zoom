<?php

namespace laraSDKs\Zoom\Enums;

/**
 * Enum for Zoom User Types.
 */
enum UserType: int
{
    case BASIC = 1;
    case LICENSED = 2;
    case ON_PREM = 3;
    case NONE = 99;

    /**
     * Get the human-readable name for the user type.
     */
    public function getName(): string
    {
        return match ($this) {
            self::BASIC => 'Basic',
            self::LICENSED => 'Licensed',
            self::ON_PREM => 'On-Prem',
            self::NONE => 'None',
        };
    }

    /**
     * Check if the user type is licensed.
     */
    public function isLicensed(): bool
    {
        return $this === self::LICENSED;
    }

    /**
     * Check if the user type is basic.
     */
    public function isBasic(): bool
    {
        return $this === self::BASIC;
    }

    /**
     * Check if the user type is on-prem.
     */
    public function isOnPrem(): bool
    {
        return $this === self::ON_PREM;
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
