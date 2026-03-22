<?php

namespace App\Domain\Organizations\Data;

final readonly class CreateOrganizationData
{
    public function __construct(
        public string $name,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
        );
    }
}