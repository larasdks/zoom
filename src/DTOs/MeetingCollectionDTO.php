<?php

namespace laraSDKs\Zoom\DTOs;

use ArrayAccess;
use Countable;
use Iterator;
use laraSDKs\Zoom\Enums\MeetingType;

/**
 * Collection of MeetingDTO objects with pagination support.
 */
class MeetingCollectionDTO implements ArrayAccess, Countable, Iterator
{
    private int $position = 0;

    /**
     * @param  MeetingDTO[]  $meetings
     */
    public function __construct(
        private array $meetings = [],
        public readonly ?int $pageCount = null,
        public readonly ?int $pageNumber = null,
        public readonly ?int $pageSize = null,
        public readonly ?int $totalRecords = null,
        public readonly ?string $nextPageToken = null
    ) {}

    /**
     * Create a MeetingCollectionDTO from API response.
     */
    public static function fromArray(array $data, ?array $pagination = null): self
    {
        $meetings = [];

        // Handle both direct meeting list and nested 'meetings' key
        $meetingList = $data['meetings'] ?? $data;

        if (is_array($meetingList)) {
            foreach ($meetingList as $meetingData) {
                if (is_array($meetingData)) {
                    $meetings[] = MeetingDTO::fromArray($meetingData);
                }
            }
        }

        return new self(
            meetings: $meetings,
            pageCount: $pagination['page_count'] ?? null,
            pageNumber: $pagination['page_number'] ?? null,
            pageSize: $pagination['page_size'] ?? null,
            totalRecords: $pagination['total_records'] ?? null,
            nextPageToken: $pagination['next_page_token'] ?? null
        );
    }

    /**
     * Get all meetings in the collection.
     *
     * @return MeetingDTO[]
     */
    public function all(): array
    {
        return $this->meetings;
    }

    /**
     * Get the first meeting in the collection.
     */
    public function first(): ?MeetingDTO
    {
        return $this->meetings[0] ?? null;
    }

    /**
     * Check if there are more pages available.
     */
    public function hasMorePages(): bool
    {
        return ! empty($this->nextPageToken);
    }

    /**
     * Filter meetings by a callback.
     */
    public function filter(callable $callback): self
    {
        return new self(
            meetings: array_filter($this->meetings, $callback),
            pageCount: $this->pageCount,
            pageNumber: $this->pageNumber,
            pageSize: $this->pageSize,
            totalRecords: $this->totalRecords,
            nextPageToken: $this->nextPageToken
        );
    }

    /**
     * Map meetings through a callback.
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->meetings);
    }

    /**
     * Filter meetings by type.
     */
    public function byType(MeetingType $type): self
    {
        return $this->filter(fn (MeetingDTO $meeting) => $meeting->type === $type);
    }

    /**
     * Get only scheduled meetings.
     */
    public function scheduled(): self
    {
        return $this->filter(fn (MeetingDTO $meeting) => $meeting->isScheduled());
    }

    /**
     * Get only recurring meetings.
     */
    public function recurring(): self
    {
        return $this->filter(fn (MeetingDTO $meeting) => $meeting->isRecurring());
    }

    /**
     * Get only upcoming meetings.
     */
    public function upcoming(): self
    {
        return $this->filter(fn (MeetingDTO $meeting) => ! $meeting->hasStarted());
    }

    /**
     * Get only past meetings.
     */
    public function past(): self
    {
        return $this->filter(fn (MeetingDTO $meeting) => $meeting->hasEnded());
    }

    /**
     * Get only meetings currently in progress.
     */
    public function inProgress(): self
    {
        return $this->filter(fn (MeetingDTO $meeting) => $meeting->isInProgress());
    }

    /**
     * Convert collection to array.
     */
    public function toArray(): array
    {
        return [
            'meetings' => array_map(fn (MeetingDTO $meeting) => $meeting->toArray(), $this->meetings),
            'pagination' => [
                'page_count' => $this->pageCount,
                'page_number' => $this->pageNumber,
                'page_size' => $this->pageSize,
                'total_records' => $this->totalRecords,
                'next_page_token' => $this->nextPageToken,
            ],
        ];
    }

    // Iterator implementation
    public function current(): MeetingDTO
    {
        return $this->meetings[$this->position];
    }

    public function key(): int
    {
        return $this->position;
    }

    public function next(): void
    {
        $this->position++;
    }

    public function rewind(): void
    {
        $this->position = 0;
    }

    public function valid(): bool
    {
        return isset($this->meetings[$this->position]);
    }

    // Countable implementation
    public function count(): int
    {
        return count($this->meetings);
    }

    // ArrayAccess implementation
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->meetings[$offset]);
    }

    public function offsetGet(mixed $offset): ?MeetingDTO
    {
        return $this->meetings[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($value instanceof MeetingDTO) {
            if (is_null($offset)) {
                $this->meetings[] = $value;
            } else {
                $this->meetings[$offset] = $value;
            }
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->meetings[$offset]);
    }
}
