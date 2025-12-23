<?php

namespace laraSDKs\Zoom\Exceptions;

use Exception;

/**
 * Base exception for all Zoom API errors.
 */
class ZoomApiException extends Exception
{
    protected ?array $errors = null;

    public function __construct(string $message = '', int $code = 0, ?array $errors = null)
    {
        parent::__construct($message, $code);
        $this->errors = $errors;
    }

    public function getErrors(): ?array
    {
        return $this->errors;
    }
}
