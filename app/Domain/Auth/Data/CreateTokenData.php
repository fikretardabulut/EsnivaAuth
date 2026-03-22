<?php

namespace App\Domain\Auth\Data;

use App\Domain\Auth\Support\TokenAbility;

final readonly class CreateTokenData
{
    public function __construct(
        public string $name,
        public array $abilities,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            name: $data['name'],
            abilities: $data['abilities'] ?? TokenAbility::default(),
        );
    }
}