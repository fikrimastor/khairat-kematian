<?php

namespace App\DTO\Member;

use App\Enums\UserStatus;
use Illuminate\Support\Carbon;

class MemberData
{
    public function __construct(
        public readonly ?int $id,
        public readonly string $name,
        public readonly string $email,
        public readonly ?string $address = null,
        public readonly ?string $phone = null,
        public readonly ?string $identificationNumber = null,
        public readonly bool $isAdmin = false,
        public readonly string $language = 'ms',
        public readonly UserStatus $status = UserStatus::ACTIVE,
        public readonly ?Carbon $createdAt = null,
        public readonly ?Carbon $updatedAt = null,
    ) {}

    /**
     * Create a DTO from an array of data.
     *
     * @param  array  $data
     * @return static
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'] ?? null,
            name: $data['name'],
            email: $data['email'],
            address: $data['address'] ?? null,
            phone: $data['phone'] ?? null,
            identificationNumber: $data['identification_number'] ?? null,
            isAdmin: $data['is_admin'] ?? false,
            language: $data['language'] ?? 'ms',
            status: isset($data['status'])
                ? (is_string($data['status']) ? UserStatus::from($data['status']) : $data['status'])
                : UserStatus::ACTIVE,
            createdAt: isset($data['created_at'])
                ? (is_string($data['created_at']) ? Carbon::parse($data['created_at']) : $data['created_at'])
                : null,
            updatedAt: isset($data['updated_at'])
                ? (is_string($data['updated_at']) ? Carbon::parse($data['updated_at']) : $data['updated_at'])
                : null,
        );
    }

    /**
     * Convert the DTO to an array.
     *
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'address' => $this->address,
            'phone' => $this->phone,
            'identification_number' => $this->identificationNumber,
            'is_admin' => $this->isAdmin,
            'language' => $this->language,
            'status' => $this->status->value,
            'created_at' => $this->createdAt?->toDateTimeString(),
            'updated_at' => $this->updatedAt?->toDateTimeString(),
        ];
    }
}
