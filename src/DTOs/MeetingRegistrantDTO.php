<?php

namespace laraSDKs\Zoom\DTOs;

use DateTime;

/**
 * Data Transfer Object for Zoom Meeting Registrant.
 */
class MeetingRegistrantDTO
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly ?string $firstName = null,
        public readonly ?string $lastName = null,
        public readonly ?string $address = null,
        public readonly ?string $city = null,
        public readonly ?string $country = null,
        public readonly ?string $zip = null,
        public readonly ?string $state = null,
        public readonly ?string $phone = null,
        public readonly ?string $industry = null,
        public readonly ?string $org = null,
        public readonly ?string $jobTitle = null,
        public readonly ?string $purchasingTimeFrame = null,
        public readonly ?string $roleInPurchaseProcess = null,
        public readonly ?int $noOfEmployees = null,
        public readonly ?string $comments = null,
        public readonly ?array $customQuestions = null,
        public readonly ?string $status = null,
        public readonly ?DateTime $createTime = null,
        public readonly ?string $joinUrl = null,
        public readonly ?string $registrantId = null,
        public readonly array $rawData = []
    ) {}

    /**
     * Create a MeetingRegistrantDTO from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            email: $data['email'] ?? '',
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            address: $data['address'] ?? null,
            city: $data['city'] ?? null,
            country: $data['country'] ?? null,
            zip: $data['zip'] ?? null,
            state: $data['state'] ?? null,
            phone: $data['phone'] ?? null,
            industry: $data['industry'] ?? null,
            org: $data['org'] ?? null,
            jobTitle: $data['job_title'] ?? null,
            purchasingTimeFrame: $data['purchasing_time_frame'] ?? null,
            roleInPurchaseProcess: $data['role_in_purchase_process'] ?? null,
            noOfEmployees: $data['no_of_employees'] ?? null,
            comments: $data['comments'] ?? null,
            customQuestions: $data['custom_questions'] ?? null,
            status: $data['status'] ?? null,
            createTime: isset($data['create_time']) ? new DateTime($data['create_time']) : null,
            joinUrl: $data['join_url'] ?? null,
            registrantId: $data['registrant_id'] ?? null,
            rawData: $data
        );
    }

    /**
     * Get the full name of the registrant.
     */
    public function getFullName(): string
    {
        $parts = array_filter([
            $this->firstName,
            $this->lastName,
        ]);

        return implode(' ', $parts) ?: $this->email;
    }

    /**
     * Check if registrant is approved.
     */
    public function isApproved(): bool
    {
        return $this->status === 'approved';
    }

    /**
     * Check if registrant is denied.
     */
    public function isDenied(): bool
    {
        return $this->status === 'denied';
    }

    /**
     * Check if registrant is pending.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Convert the DTO to an array.
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'address' => $this->address,
            'city' => $this->city,
            'country' => $this->country,
            'zip' => $this->zip,
            'state' => $this->state,
            'phone' => $this->phone,
            'industry' => $this->industry,
            'org' => $this->org,
            'job_title' => $this->jobTitle,
            'purchasing_time_frame' => $this->purchasingTimeFrame,
            'role_in_purchase_process' => $this->roleInPurchaseProcess,
            'no_of_employees' => $this->noOfEmployees,
            'comments' => $this->comments,
            'custom_questions' => $this->customQuestions,
            'status' => $this->status,
            'create_time' => $this->createTime?->format('c'),
            'join_url' => $this->joinUrl,
            'registrant_id' => $this->registrantId,
        ];
    }
}
