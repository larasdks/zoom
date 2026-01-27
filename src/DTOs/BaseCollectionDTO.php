<?php

namespace laraSDKs\Zoom\DTOs;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Base collection class for paginated Zoom API responses.
 *
 * @template T
 * @implements Iterator<int, T>
 * @implements ArrayAccess<int, T>
 */
abstract class BaseCollectionDTO implements ArrayAccess, Countable, Iterator
{
    private int $position = 0;

    /**
     * @param  T[]  $items
     */
    public function __construct(
        protected array $items = [],
        public readonly ?int $pageCount = null,
        public readonly ?int $pageNumber = null,
        public readonly ?int $pageSize = null,
        public readonly ?int $totalRecords = null,
        public readonly ?string $nextPageToken = null
    ) {}

    /**
     * Get all items in the collection.
     *
     * @return T[]
     */
    public function all(): array
    {
        return $this->items;
    }

    /**
     * Get the first item in the collection.
     *
     * @return T|null
     */
    public function first(): mixed
    {
        return $this->items[0] ?? null;
    }

    /**
     * Get the last item in the collection.
     *
     * @return T|null
     */
    public function last(): mixed
    {
        return empty($this->items) ? null : $this->items[array_key_last($this->items)];
    }

    /**
     * Check if the collection is empty.
     */
    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    /**
     * Check if the collection is not empty.
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }

    /**
     * Check if there are more pages available.
     */
    public function hasMorePages(): bool
    {
        return ! empty($this->nextPageToken);
    }

    /**
     * Filter items by a callback.
     *
     * @param callable $callback
     * @return static
     */
    public function filter(callable $callback): static
    {
        return new static(
            items: array_filter($this->items, $callback),
            pageCount: $this->pageCount,
            pageNumber: $this->pageNumber,
            pageSize: $this->pageSize,
            totalRecords: $this->totalRecords,
            nextPageToken: $this->nextPageToken
        );
    }

    /**
     * Map items through a callback.
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->items);
    }

    /**
     * Get pagination info as an array.
     */
    public function getPaginationInfo(): array
    {
        return [
            'page_count' => $this->pageCount,
            'page_number' => $this->pageNumber,
            'page_size' => $this->pageSize,
            'total_records' => $this->totalRecords,
            'next_page_token' => $this->nextPageToken,
        ];
    }

    /**
     * Convert collection to array.
     * Child classes should override this to customize the structure.
     */
    abstract public function toArray(): array;

    // Iterator implementation
    public function current(): mixed
    {
        return $this->items[$this->position];
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
        return isset($this->items[$this->position]);
    }

    // Countable implementation
    public function count(): int
    {
        return count($this->items);
    }

    // ArrayAccess implementation
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->items[$offset]);
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->items[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if (is_null($offset)) {
            $this->items[] = $value;
        } else {
            $this->items[$offset] = $value;
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->items[$offset]);
    }
}
