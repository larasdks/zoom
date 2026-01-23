<?php

namespace laraSDKs\Zoom\DTOs;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Collection of MeetingRegistrantDTO objects with pagination support.
 */
class MeetingRegistrantCollectionDTO implements ArrayAccess, Countable, Iterator
{
    private int $position = 0;

    /**
     * @param  MeetingRegistrantDTO[]  $registrants
     */
    public function __construct(
        private array $registrants = [],
        public readonly ?int $pageCount = null,
        public readonly ?int $pageNumber = null,
        public readonly ?int $pageSize = null,
        public readonly ?int $totalRecords = null,
        public readonly ?string $nextPageToken = null
    ) {}

    /**
     * Create a MeetingRegistrantCollectionDTO from API response.
     */
    public static function fromArray(array $data, ?array $pagination = null): self
    {
        $registrants = [];

        // Handle both direct registrant list and nested 'registrants' key
        $registrantList = $data['registrants'] ?? $data;

        if (is_array($registrantList)) {
            foreach ($registrantList as $registrantData) {
                if (is_array($registrantData)) {
                    $registrants[] = MeetingRegistrantDTO::fromArray($registrantData);
                }
            }
        }

        return new self(
            registrants: $registrants,
            pageCount: $pagination['page_count'] ?? null,
            pageNumber: $pagination['page_number'] ?? null,
            pageSize: $pagination['page_size'] ?? null,
            totalRecords: $pagination['total_records'] ?? null,
            nextPageToken: $pagination['next_page_token'] ?? null
        );
    }

    /**
     * Get all registrants in the collection.
     *
     * @return MeetingRegistrantDTO[]
     */
    public function all(): array
    {
        return $this->registrants;
    }

    /**
     * Get the first registrant in the collection.
     */
    public function first(): ?MeetingRegistrantDTO
    {
        return $this->registrants[0] ?? null;
    }

    /**
     * Check if there are more pages available.
     */
    public function hasMorePages(): bool
    {
        return ! empty($this->nextPageToken);
    }

    /**
     * Filter registrants by a callback.
     */
    public function filter(callable $callback): self
    {
        return new self(
            registrants: array_filter($this->registrants, $callback),
            pageCount: $this->pageCount,
            pageNumber: $this->pageNumber,
            pageSize: $this->pageSize,
            totalRecords: $this->totalRecords,
            nextPageToken: $this->nextPageToken
        );
    }

    /**
     * Map registrants through a callback.
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->registrants);
    }

    /**
     * Get only approved registrants.
     */
    public function approved(): self
    {
        return $this->filter(fn (MeetingRegistrantDTO $registrant) => $registrant->isApproved());
    }

    /**
     * Get only denied registrants.
     */
    public function denied(): self
    {
        return $this->filter(fn (MeetingRegistrantDTO $registrant) => $registrant->isDenied());
    }

    /**
     * Get only pending registrants.
     */
    public function pending(): self
    {
        return $this->filter(fn (MeetingRegistrantDTO $registrant) => $registrant->isPending());
    }

    /**
     * Convert collection to array.
     */
    public function toArray(): array
    {
        return [
            'registrants' => array_map(fn (MeetingRegistrantDTO $registrant) => $registrant->toArray(), $this->registrants),
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
    public function current(): MeetingRegistrantDTO
    {
        return $this->registrants[$this->position];
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
        return isset($this->registrants[$this->position]);
    }

    // Countable implementation
    public function count(): int
    {
        return count($this->registrants);
    }

    // ArrayAccess implementation
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->registrants[$offset]);
    }

    public function offsetGet(mixed $offset): ?MeetingRegistrantDTO
    {
        return $this->registrants[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($value instanceof MeetingRegistrantDTO) {
            if (is_null($offset)) {
                $this->registrants[] = $value;
            } else {
                $this->registrants[$offset] = $value;
            }
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->registrants[$offset]);
    }
}
