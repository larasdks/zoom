<?php

namespace laraSDKs\Zoom\DTOs;

/**
 * Collection of MeetingRegistrantDTO objects with pagination support.
 *
 * @extends BaseCollectionDTO<MeetingRegistrantDTO>
 */
class MeetingRegistrantCollectionDTO extends BaseCollectionDTO
{

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
            items: $registrants,
            pageCount: $pagination['page_count'] ?? null,
            pageNumber: $pagination['page_number'] ?? null,
            pageSize: $pagination['page_size'] ?? null,
            totalRecords: $pagination['total_records'] ?? null,
            nextPageToken: $pagination['next_page_token'] ?? null
        );
    }

    /**
     * Get all registrants (alias for all()).
     *
     * @return MeetingRegistrantDTO[]
     */
    public function registrants(): array
    {
        return $this->all();
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
            'registrants' => array_map(fn (MeetingRegistrantDTO $registrant) => $registrant->toArray(), $this->items),
            'pagination' => $this->getPaginationInfo(),
        ];
    }
}
