<?php

namespace laraSDKs\Zoom\DTOs;

use laraSDKs\Zoom\Enums\MeetingType;

/**
 * Collection of MeetingDTO objects with pagination support.
 *
 * @extends BaseCollectionDTO<MeetingDTO>
 */
class MeetingCollectionDTO extends BaseCollectionDTO
{

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
            items: $meetings,
            pageCount: $pagination['page_count'] ?? null,
            pageNumber: $pagination['page_number'] ?? null,
            pageSize: $pagination['page_size'] ?? null,
            totalRecords: $pagination['total_records'] ?? null,
            nextPageToken: $pagination['next_page_token'] ?? null
        );
    }

    /**
     * Get all meetings (alias for all()).
     *
     * @return MeetingDTO[]
     */
    public function meetings(): array
    {
        return $this->all();
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
            'meetings' => array_map(fn (MeetingDTO $meeting) => $meeting->toArray(), $this->items),
            'pagination' => $this->getPaginationInfo(),
        ];
    }
}
