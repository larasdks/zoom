<?php

namespace laraSDKs\Zoom\DTOs;

use laraSDKs\Zoom\Enums\UserType;

/**
 * Collection of UserDTO objects with pagination support.
 *
 * @extends BaseCollectionDTO<UserDTO>
 */
class UserCollectionDTO extends BaseCollectionDTO
{

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
            items: $users,
            pageCount: $pagination['page_count'] ?? null,
            pageNumber: $pagination['page_number'] ?? null,
            pageSize: $pagination['page_size'] ?? null,
            totalRecords: $pagination['total_records'] ?? null,
            nextPageToken: $pagination['next_page_token'] ?? null
        );
    }

    /**
     * Get all users (alias for all()).
     *
     * @return UserDTO[]
     */
    public function users(): array
    {
        return $this->all();
    }

    /**
     * Filter users by type.
     */
    public function byType(UserType $type): self
    {
        return $this->filter(fn (UserDTO $user) => $user->type === $type);
    }

    /**
     * Get only licensed users.
     */
    public function licensed(): self
    {
        return $this->filter(fn (UserDTO $user) => $user->isLicensed());
    }

    /**
     * Get only basic users.
     */
    public function basic(): self
    {
        return $this->filter(fn (UserDTO $user) => $user->type === UserType::BASIC);
    }

    /**
     * Get only active users.
     */
    public function active(): self
    {
        return $this->filter(fn (UserDTO $user) => $user->isActive());
    }

    /**
     * Get only verified users.
     */
    public function verified(): self
    {
        return $this->filter(fn (UserDTO $user) => $user->isVerified());
    }

    /**
     * Convert collection to array.
     */
    public function toArray(): array
    {
        return [
            'users' => array_map(fn (UserDTO $user) => $user->toArray(), $this->items),
            'pagination' => $this->getPaginationInfo(),
        ];
    }
}
