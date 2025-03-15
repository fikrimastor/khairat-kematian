<?php

namespace App\DTO\Member;

use App\Enums\RelationshipType;
use Illuminate\Support\Carbon;

class DependentData
{
    public function __construct(
        public readonly ?int $id,
        public readonly int $userId,
        public readonly string $name,
        public readonly ?string $identificationNumber = null,
        public readonly ?Carbon $birthDate = null,
        public readonly ?RelationshipType $relationship = null,
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
            userId: $data['user_id'],
            name: $data['name'],
            identificationNumber: $data['identification_number'] ?? null,
            birthDate: isset($data['birth_date'])
                ? (is_string($data['birth_date']) ? Carbon::parse($data['birth_date']) : $data['birth_date'])
                : null,
            relationship: isset($data['relationship'])
                ? (is_string($data['relationship']) ? RelationshipType::from($data['relationship']) : $data['relationship'])
                : null,
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
            'user_id' => $this->userId,
            'name' => $this->name,
            'identification_number' => $this->identificationNumber,
            'birth_date' => $this->birthDate?->toDateString(),
            'relationship' => $this->relationship?->value,
            'created_at' => $this->createdAt?->toDateTimeString(),
            'updated_at' => $this->updatedAt?->toDateTimeString(),
        ];
    }
}
