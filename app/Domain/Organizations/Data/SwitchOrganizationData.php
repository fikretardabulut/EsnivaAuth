<?php

namespace App\Domain\Organizations\Data;

final readonly class SwitchOrganizationData
{
    public function __construct(
        public int $organizationId,
    ) {
    }

    public static function fromArray(array $data): self
    {
        return new self(
            organizationId: (int) $data['organization_id'],
        );
    }
}