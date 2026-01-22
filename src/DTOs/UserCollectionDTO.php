<?php

namespace laraSDKs\Zoom\DTOs;

use ArrayAccess;
use Countable;
use Iterator;

/**
 * Collection of UserDTO objects with pagination support.
 */
class UserCollectionDTO implements ArrayAccess, Countable, Iterator
{
    private int $position = 0;

    /**
     * @param  UserDTO[]  $users
     */
    public function __construct(
        private array $users = [],
        public readonly ?int $pageCount = null,
        public readonly ?int $pageNumber = null,
        public readonly ?int $pageSize = null,
        public readonly ?int $totalRecords = null,
        public readonly ?string $nextPageToken = null
    ) {}

    /**
     * Create a UserCollectionDTO from API response.
     */
    public static function fromArray(array $data, ?array $pagination = null): self
    {
        $users = [];
        // Handle both direct user list and nested 'users' key
        $userList = $data['users'] ?? $data;

        if (is_array($userList)) {
            foreach ($userList as $userData) {
                if (is_array($userData)) {
                    $users[] = UserDTO::fromArray($userData);
                }
            }
        }

        return new self(
            users: $users,
            pageCount: $pagination['page_count'] ?? null,
            pageNumber: $pagination['page_number'] ?? null,
            pageSize: $pagination['page_size'] ?? null,
            totalRecords: $pagination['total_records'] ?? null,
            nextPageToken: $pagination['next_page_token'] ?? null
        );
    }

    /**
     * Get all users in the collection.
     *
     * @return UserDTO[]
     */
    public function all(): array
    {
        return $this->users;
    }

    /**
     * Get the first user in the collection.
     */
    public function first(): ?UserDTO
    {
        return $this->users[0] ?? null;
    }

    /**
     * Check if there are more pages available.
     */
    public function hasMorePages(): bool
    {
        return ! empty($this->nextPageToken);
    }

    /**
     * Filter users by a callback.
     */
    public function filter(callable $callback): self
    {
        return new self(
            users: array_filter($this->users, $callback),
            pageCount: $this->pageCount,
            pageNumber: $this->pageNumber,
            pageSize: $this->pageSize,
            totalRecords: $this->totalRecords,
            nextPageToken: $this->nextPageToken
        );
    }

    /**
     * Map users through a callback.
     */
    public function map(callable $callback): array
    {
        return array_map($callback, $this->users);
    }

    /**
     * Convert collection to array.
     */
    public function toArray(): array
    {
        return [
            'users' => array_map(fn (UserDTO $user) => $user->toArray(), $this->users),
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
    public function current(): UserDTO
    {
        return $this->users[$this->position];
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
        return isset($this->users[$this->position]);
    }

    // Countable implementation
    public function count(): int
    {
        return count($this->users);
    }

    // ArrayAccess implementation
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->users[$offset]);
    }

    public function offsetGet(mixed $offset): ?UserDTO
    {
        return $this->users[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        if ($value instanceof UserDTO) {
            if (is_null($offset)) {
                $this->users[] = $value;
            } else {
                $this->users[$offset] = $value;
            }
        }
    }

    public function offsetUnset(mixed $offset): void
    {
        unset($this->users[$offset]);
    }
}
