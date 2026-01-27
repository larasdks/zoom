<?php

namespace laraSDKs\Zoom\DTOs;

use laraSDKs\Zoom\Enums\ParticipantStatus;

/**
 * Collection of ParticipantDTO objects with pagination support.
 *
 * @extends BaseCollectionDTO<ParticipantDTO>
 */
class ParticipantCollectionDTO extends BaseCollectionDTO
{

    /**
     * Create a ParticipantCollectionDTO from API response.
     */
    public static function fromArray(array $data, ?array $pagination = null): self
    {
        $participants = [];

        // Handle both direct participant list and nested 'participants' key
        $participantList = $data['participants'] ?? $data;

        if (is_array($participantList)) {
            foreach ($participantList as $participantData) {
                if (is_array($participantData)) {
                    $participants[] = ParticipantDTO::fromArray($participantData);
                }
            }
        }

        return new self(
            items: $participants,
            pageCount: $pagination['page_count'] ?? null,
            pageNumber: $pagination['page_number'] ?? null,
            pageSize: $pagination['page_size'] ?? null,
            totalRecords: $pagination['total_records'] ?? null,
            nextPageToken: $pagination['next_page_token'] ?? null
        );
    }

    /**
     * Get all participants (alias for all()).
     *
     * @return ParticipantDTO[]
     */
    public function participants(): array
    {
        return $this->all();
    }

    /**
     * Get participants still in the meeting (haven't left yet).
     */
    public function stillInMeeting(): self
    {
        return $this->filter(fn (ParticipantDTO $participant) => $participant->isInMeeting());
    }

    /**
     * Get participants who have left.
     */
    public function left(): self
    {
        return $this->filter(fn (ParticipantDTO $participant) => $participant->hasLeft());
    }

    /**
     * Get participants with "in_meeting" status.
     */
    public function withInMeetingStatus(): self
    {
        return $this->filter(fn (ParticipantDTO $participant) => $participant->status === ParticipantStatus::IN_MEETING);
    }

    /**
     * Get participants in the waiting room.
     */
    public function inWaitingRoom(): self
    {
        return $this->filter(fn (ParticipantDTO $participant) => $participant->isInWaitingRoom());
    }

    /**
     * Filter by status.
     */
    public function byStatus(ParticipantStatus $status): self
    {
        return $this->filter(fn (ParticipantDTO $participant) => $participant->status === $status);
    }

    /**
     * Get participants who were/are in breakout rooms.
     */
    public function inBreakoutRooms(): self
    {
        return $this->filter(fn (ParticipantDTO $participant) => $participant->isInBreakoutRoom());
    }

    /**
     * Get participants who experienced failover.
     */
    public function withFailover(): self
    {
        return $this->filter(fn (ParticipantDTO $participant) => $participant->failover === true);
    }

    /**
     * Get total duration for all participants (in seconds).
     */
    public function getTotalDuration(): int
    {
        return array_reduce(
            $this->items,
            fn (int $carry, ParticipantDTO $participant) => $carry + ($participant->duration ?? 0),
            0
        );
    }

    /**
     * Get average duration per participant (in seconds).
     */
    public function getAverageDuration(): float
    {
        if ($this->isEmpty()) {
            return 0;
        }

        return $this->getTotalDuration() / $this->count();
    }

    /**
     * Get participant with the longest duration.
     */
    public function getLongestDuration(): ?ParticipantDTO
    {
        if ($this->isEmpty()) {
            return null;
        }

        return array_reduce(
            $this->items,
            fn (?ParticipantDTO $max, ParticipantDTO $participant) => $max === null || ($participant->duration ?? 0) > ($max->duration ?? 0) ? $participant : $max
        );
    }

    /**
     * Get participant with the shortest duration.
     */
    public function getShortestDuration(): ?ParticipantDTO
    {
        if ($this->isEmpty()) {
            return null;
        }

        return array_reduce(
            $this->items,
            fn (?ParticipantDTO $min, ParticipantDTO $participant) => $min === null || ($participant->duration ?? PHP_INT_MAX) < ($min->duration ?? PHP_INT_MAX) ? $participant : $min
        );
    }

    /**
     * Convert collection to array.
     */
    public function toArray(): array
    {
        return [
            'participants' => array_map(fn (ParticipantDTO $participant) => $participant->toArray(), $this->items),
            'pagination' => $this->getPaginationInfo(),
        ];
    }
}
