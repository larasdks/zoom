<?php

namespace laraSDKs\Zoom\DTOs;

use Carbon\Carbon;
use laraSDKs\Zoom\Enums\UserType;

/**
 * Data Transfer Object for Zoom User.
 */
readonly class UserDTO
{
    public function __construct(
        public string $id,
        public string $email,
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $displayName = null,
        public ?UserType $type = null,
        public ?string $status = null,
        public ?int $pmi = null,
        public ?string $timezone = null,
        public ?int $verified = null,
        public ?Carbon $userCreatedAt = null,
        public ?Carbon $lastLoginTime = null,
        public ?string $language = null,
        public ?string $phoneNumber = null,
        public ?string $phoneCountry = null,
        public ?string $vanityUrl = null,
        public ?string $personalMeetingUrl = null,
        public ?string $picUrl = null,
        public ?string $hostKey = null,
        public ?string $jid = null,
        public array $groupIds = [],
        public array $divisionIds = [],
        public array $imGroupIds = [],
        public ?string $accountId = null,
        public ?int $cmrUserId = null,
        public ?string $dept = null,
        public ?string $jobTitle = null,
        public ?string $location = null,
        public ?string $roleId = null,
        public ?string $company = null,
        public ?bool $usePasswordManagementInterface = null,
        public ?string $clusterName = null,
        public ?string $planUnitedType = null,
        public ?string $employeeUniqueId = null,
        public ?string $lastClientVersion = null,
        public array $customAttributes = [],
        public array $licenseInfoList = [],
        public array $rawData = []
    ) {}

    /**
     * Create a UserDTO from API response array.
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? '',
            email: $data['email'] ?? '',
            firstName: $data['first_name'] ?? null,
            lastName: $data['last_name'] ?? null,
            displayName: $data['display_name'] ?? null,
            type: UserType::tryFromInt($data['type'] ?? null),
            status: $data['status'] ?? null,
            pmi: $data['pmi'] ?? null,
            timezone: $data['timezone'] ?? null,
            verified: $data['verified'] ?? null,
            userCreatedAt: isset($data['user_created_at']) ? Carbon::parse($data['user_created_at']) :
                          (isset($data['created_at']) ? Carbon::parse($data['created_at']) : null),
            lastLoginTime: isset($data['last_login_time']) ? Carbon::parse($data['last_login_time']) : null,
            language: $data['language'] ?? null,
            phoneNumber: $data['phone_number'] ?? null,
            phoneCountry: $data['phone_country'] ?? null,
            vanityUrl: $data['vanity_url'] ?? null,
            personalMeetingUrl: $data['personal_meeting_url'] ?? null,
            picUrl: $data['pic_url'] ?? null,
            hostKey: $data['host_key'] ?? null,
            jid: $data['jid'] ?? null,
            groupIds: $data['group_ids'] ?? [],
            divisionIds: $data['division_ids'] ?? [],
            imGroupIds: $data['im_group_ids'] ?? [],
            accountId: $data['account_id'] ?? null,
            cmrUserId: $data['cmr_user_id'] ?? null,
            dept: $data['dept'] ?? null,
            jobTitle: $data['job_title'] ?? null,
            location: $data['location'] ?? null,
            roleId: $data['role_id'] ?? null,
            company: $data['company'] ?? null,
            usePasswordManagementInterface: $data['use_pmi'] ?? null,
            clusterName: $data['cluster'] ?? null,
            planUnitedType: $data['plan_united_type'] ?? null,
            employeeUniqueId: $data['employee_unique_id'] ?? null,
            lastClientVersion: $data['last_client_version'] ?? null,
            customAttributes: $data['custom_attributes'] ?? [],
            licenseInfoList: $data['license_info_list'] ?? [],
            rawData: $data
        );
    }

    /**
     * Get the full name of the user.
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
     * Get the user type name.
     */
    public function getUserTypeName(): string
    {
        return $this->type?->getName() ?? 'Unknown';
    }

    /**
     * Check if the user is verified.
     */
    public function isVerified(): bool
    {
        return $this->verified === 1;
    }

    /**
     * Check if the user is active.
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Check if the user is licensed.
     */
    public function isLicensed(): bool
    {
        return $this->type?->isLicensed() ?? false;
    }

    /**
     * Get custom attribute by key or name.
     */
    public function getCustomAttribute(string $keyOrName): ?array
    {
        foreach ($this->customAttributes as $attribute) {
            if (($attribute['key'] ?? '') === $keyOrName || ($attribute['name'] ?? '') === $keyOrName) {
                return $attribute;
            }
        }

        return null;
    }

    /**
     * Get custom attribute value by key or name.
     */
    public function getCustomAttributeValue(string $keyOrName): ?string
    {
        $attribute = $this->getCustomAttribute($keyOrName);

        return $attribute['value'] ?? null;
    }

    /**
     * Check if user has a specific license type.
     */
    public function hasLicenseType(string $licenseType): bool
    {
        foreach ($this->licenseInfoList as $license) {
            if (($license['license_type'] ?? '') === $licenseType) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get all license types.
     */
    public function getLicenseTypes(): array
    {
        return array_map(fn ($license) => $license['license_type'] ?? '', $this->licenseInfoList);
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
            'display_name' => $this->displayName,
            'type' => $this->type?->value,
            'status' => $this->status,
            'pmi' => $this->pmi,
            'timezone' => $this->timezone,
            'verified' => $this->verified,
            'user_created_at' => $this->userCreatedAt?->format('c'),
            'last_login_time' => $this->lastLoginTime?->format('c'),
            'language' => $this->language,
            'phone_number' => $this->phoneNumber,
            'phone_country' => $this->phoneCountry,
            'vanity_url' => $this->vanityUrl,
            'personal_meeting_url' => $this->personalMeetingUrl,
            'pic_url' => $this->picUrl,
            'host_key' => $this->hostKey,
            'jid' => $this->jid,
            'group_ids' => $this->groupIds,
            'division_ids' => $this->divisionIds,
            'im_group_ids' => $this->imGroupIds,
            'account_id' => $this->accountId,
            'cmr_user_id' => $this->cmrUserId,
            'dept' => $this->dept,
            'job_title' => $this->jobTitle,
            'location' => $this->location,
            'role_id' => $this->roleId,
            'company' => $this->company,
            'use_pmi' => $this->usePasswordManagementInterface,
            'cluster' => $this->clusterName,
            'plan_united_type' => $this->planUnitedType,
            'employee_unique_id' => $this->employeeUniqueId,
            'last_client_version' => $this->lastClientVersion,
            'custom_attributes' => $this->customAttributes,
            'license_info_list' => $this->licenseInfoList,
        ];
    }
}
